<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\IAddProductFormFactory;
use App\Model\AddProductService;
use App\Model\Category;
use App\Model\Product;
use App\Model\ProductParameter;
use App\Model\Session\CartService;
use Nette\Application\UI\Form;
use Nette\Caching\Cache;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Nette\Utils\Html;
use Nette\Utils\Paginator;
use Nextras\Dbal\Result\Result;
use Tracy\Debugger;

class SparePartsPresenter extends BasePresenter
{
    /** @var int */
    public $limit;

    /** @var array  */
    public $pages = [];

    /** @var int */
    public $lastPage;

    /** @inject */
    public IAddProductFormFactory $addProductFormFactory;

    public Category $rootCategory;

    public Result $rootCategories;

    public array $categoriesTree;

    public Category $actualCategory;

    public Product $actualProduct;

    /** @inject */
    public AddProductService $addProductService;

    public function startup()
    {
        parent::startup();
        $this->rootCategory = $this->orm->categories->getBy(["categoryLabel" => BasePresenter::INITIAL_CATEGORY_SPARE_PARTS]);
        $this->rootCategories = $this->orm->categories->getChildren($this->rootCategory->id);
    }

    public function actionDefault($page = 1, $seoName = null)
    {
        if ($seoName === null) {
            $this->actualCategory = $this->rootCategory;
        } else {
            $categoryId = explode("-", $seoName);
            if ($categoryId) {
                $categoryId = isset($categoryId[0]) ? $categoryId[0] : null;
                $category = $this->orm->categories->getById($categoryId);;
                if ($category == null) {
                    $this->redirect(":Front:Homepage:default");
                }
                $this->actualCategory = $category;
                if (!$this->actualCategory) {
                    $this->redirect("this", ["page" => 1, "seoName" => null]);
                }
            } else {
                $this->redirect("this", ["page" => 1, "seoName" => null]);
            }
        }
        $key = 'categories-tree';

        //$this->cache->remove($key);

        $this->categoriesTree = $this->cache->load($key, function (&$dependencies) use ($key) {
            $dependencies[Cache::EXPIRE] = '15 minutes';
            return $this->getCategoriesTree($this->rootCategories);
        });
    }

    public function renderDefault($page=1, $seoName = null)
    {
        $parentCategories = $this->orm->categories->getParents($this->actualCategory->id);
        $childrenCategories = $this->orm->categories->getChildren($this->actualCategory->id);
        $nextChildrenCategories = $this->orm->categories->getChildrenLevel($this->actualCategory->id, -1);

        $this->template->rootCategories = $this->rootCategories;

        $this->template->categoriesTree = $this->categoriesTree;
        $this->template->childrenCategories = $childrenCategories;

        $nextChildrenCategories = $nextChildrenCategories->fetchAll();
        foreach ($nextChildrenCategories as $index => $nextChildrenCategory) {
            if ($nextChildrenCategory->parValue !== null) {
                $count = $this->connection->query("
                SELECT count(product_categories.product_id) FROM product_categories 
                LEFT JOIN products_x_product_parameter_values ON products_x_product_parameter_values.product_id = product_categories.product_id
                 LEFT JOIN products ON products.id = product_categories.product_id                                           
                 WHERE category_id = (%i) AND product_parameter_value_id = %i AND products.visible = %b
            ", $this->actualCategory->id, $nextChildrenCategory->parValue, true)->fetchField(0);

                if ($count == 0) {
                    unset($nextChildrenCategories[$index]);
                }
            }
        }

        $this->template->nextChildrenCategories = $nextChildrenCategories;
        $this->template->parentCategories = $parentCategories;

        $this->limit = 12;
        $offset = $page>0?($page-1)*$this->limit:$page; /* -OFFSET PRODUKTŮ- */

        if ($this->actualCategory->productParameterValue !== null) {
            $productsResult = $this->getFilteredProducts([$this->actualCategory->parent->id], $this->actualCategory->productParameterValue->id);
        } else {
            $productsResult = $this->getProducts($childrenCategories->fetchPairs(null, "id"));
        }
        $ids = empty($productsResult) ? [] : $productsResult->fetchPairs(null, "product_id");

        $products = $this->orm->products->findBy([
            "id" => $ids,
            "visible" => true,
            "deleted" => false
        ]);
        $this->getTemplate()->products = $products->limitBy($this->limit,$offset);
        $this->getTemplate()->pages = $this->getPages($page, $products->countStored());
        $this->getTemplate()->actualPage = $page;
        $this->lastPage = ceil($products->countStored()/$this->limit);
        $this->getTemplate()->lastPage = $this->lastPage;
        $this->getTemplate()->actualCategory = $this->actualCategory;
        $title = '';
        foreach($parentCategories as $parentCategory) {
            $title .= $parentCategory->name . ' ';
        }
        $this->template->categoryTitle = $title;
    }

    public function renderSearch($phrase, $page=1)
    {
        $this->limit = 12;
        $offset = $page>0?($page-1)*$this->limit:$page; /* -OFFSET PRODUKTŮ- */
        $totalProductsCount = count($this->orm->products->findProducts($phrase, 9999, 0));
        $products = $this->orm->products->findProducts($phrase, $this->limit, $offset);
        $productsCount = count($products);
        $this->getTemplate()->products = $products;
        $this->getTemplate()->pages = $this->getPages($page, $totalProductsCount);
        $this->getTemplate()->actualPage = $page;
        $this->lastPage = ceil($totalProductsCount/$this->limit);
        $this->getTemplate()->lastPage = $this->lastPage;
        $this->template->categoryTitle = $phrase;
        $this->template->phrase = $phrase;
    }

    private function getProducts(array $childrenCategories): Result
    {
        return $this->orm->productCategories->getProductsInCategories($childrenCategories);
    }

    private function getFilteredProducts(array $childrenCategories, int $parameterValueId): Result
    {
        return $this->orm->productCategories->getFilteredProductsInCategories($childrenCategories, $parameterValueId);
    }

    public $tree;

    private function processCat(Category $category, $parent) {
        $parent = $parent[$category->id] = [
            'name' => $category->categoryName,
            'childs' => []
        ];
        foreach ($category->categories as $c) {
            $this->processCat($c, $parent['childs']);
        }
    }



    function createTree(&$list, $parent){
        $tree = array();
        foreach ($parent as $k=>$l){
            if(isset($list[$l['id']])){
                $l['nodes'] = $this->createTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        }
        return $tree;
    }

    private function getCategoriesTree(Result $result): array
    {
        $categories = [];
        foreach ($result as $category) {
            $isActualCategory = $this->actualCategory->id === $category->id;
            $isInPathToActualCategory = in_array($this->actualCategory->id, $this->orm->categories->getChildren($category->id)->fetchPairs(null, "id"));
            $categories[] = [
                'id' => $category->id,
                'text' => $category->name,
                'parent' => $category->parent,
                'href' => $this->link('SpareParts:default', ['page' => 1, 'seoName' => $category->url]),
                'state' => [
                    'selected' => $isActualCategory,
                    'expanded' => $isInPathToActualCategory
                ]
            ];
        }

        $tree = [];
        foreach ($categories as $category){
            $tree[$category['parent']][] = $category;
        }
        $tree = $this->createTree($tree, array($categories[0]));
        $tree = $tree[0]['nodes'];
        return $tree;
    }

    private function getCategoryTreeItemChildren($treeItem) {
        return $treeItem;
    }

    public function getPages($actualPage, $count)
    {
        $paginator = new Paginator();
        $paginator->setItemCount($count);
        $paginator->setItemsPerPage($this->limit);
        $paginator->setPage($actualPage);

        if(ceil($count/$this->limit)>5) {
            if($paginator->isLast()) $actualPage--;
            $rozdil = 4 - $actualPage;
            $actualPage <= 5 ? $actualPage = $actualPage + $rozdil + 1 : '';
            for ($i = ($actualPage - 4); $i <= ($actualPage + 1); $i++) {
                $this->pages[] = $i;
            }
        }
        else{
            for ($i = ($actualPage-($actualPage-1)); $i <= (ceil($count/$this->limit)); $i++) {
                $this->pages[] = $i;
            }
        }
        return $this->pages;
    }

    public function actionDetail($seoName)
    {
        $productId = explode("-", $seoName);
        if ($productId) {
            $productId = isset($productId[0]) ? $productId[0] : null;
            $product = $this->orm->products->getById($productId);
            if ($product == null) {
                $this->redirect(":Front:Homepage:default");
            }
            $this->actualProduct = $product;
            if (!$this->actualProduct) {
                $this->redirect("SpareParts:default", ["page" => 1, "seoName" => null]);
            }
        } else {
            $this->redirect("SpareParts:default", ["page" => 1, "seoName" => null]);
        }
    }

    public function renderDetail()
    {
        $parentCategories = $this->orm->categories->getParents($this->actualProduct->getMainCategory()->id);
        $this->getTemplate()->product = $this->actualProduct;
        $this->getTemplate()->productInCart = $this->productInCart($this->actualProduct->id);
        $this->getTemplate()->actualId = $this->actualProduct->id;
        $this->getTemplate()->session = $this->getSession()->getSection('products');
        $this->template->parentCategories = $parentCategories;
    }

    public function productInCart($id){
        return in_array($id, $this->cartService->getOrder()->ordersItems->toCollection()->findBy(['product!=' => null])->fetchPairs(null, 'product->id'));
    }

    function cesky_den($den) {
        static $nazvy = array('neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota');
        return $nazvy[$den];
    }

    protected function createComponentAddProductForm(){
        return $this->addProductFormFactory->create($this->actualProduct);
    }

    public function handleGetCategoriesTree(): void
    {
        $this->getPayload()->tree = json_encode($this->categoriesTree);
        $this->sendPayload();
    }

    public function handleAddProductToCart($id): void
    {
        $product = $this->orm->products->getById($id);
        if ($product) {
            $this->addProductService->addProduct($product->id, 1);
        }
        $this->redirect('this');
    }

    public function getProductFromRow($row)
    {
       return $this->orm->products->getById($row->id);
    }

    public function createComponentFilterForm(): Form
    {
        $form = new Form();
        $form->addCheckboxList("stockStatus", "stockStatus", [
            Product::STOCK_STATUS_ONSTOCK => "skladem",
            Product::STOCK_STATUS_TOWEEK => "více než týden",
            Product::STOCK_STATUS_WEEK => "do týdne v e-shopu",
        ]);

        $form->addCheckboxList("condition", "condition", [
            Product::CONDITION_NEW => "nové",
            Product::CONDITION_USED => "použité",
        ]);

        $form->addHidden("priceMin")
            ->setHtmlAttribute("id", "priceMin-inp");
        $form->addHidden("priceMax")
            ->setHtmlAttribute("id", "priceMax-inp");

        return $form;
    }
}