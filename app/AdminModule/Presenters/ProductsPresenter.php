<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:21
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Datagrids\ProductsDatagrid;
use App\AdminModule\Components\ProductsDatagridFactory;
use App\AdminModule\Forms\IProductFormFactory;
use App\AdminModule\Forms\IProductImagesFormFactory;
use App\AdminModule\Forms\ProductForm;
use App\AdminModule\Forms\ProductImagesForm;
use App\FrontModule\Presenters\BasePresenter;
use App\Model\Product;
use App\Model\ProductCategory;
use Nextras\Dbal\Result\Result;
use Tracy\Debugger;

class ProductsPresenter extends BaseAppPresenter
{
    /** @inject */
    public ProductsDatagridFactory $productsDatagridFactory;

    /** @inject */
    public IProductFormFactory $productFormFactory;

    /** @inject */
    public IProductImagesFormFactory $productImagesFormFactory;

    public ?Product $actualProduct;


    public function actionEdit(int $id = null): void
    {
        if ($id) {
            try {
                $this->actualProduct = $this->orm->products->getById($id);
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }
            if (!$this->actualProduct) {
                $this->redirect('default');
            }
        } else {
            $this->actualProduct = null;
        }
    }

    public function renderEdit(): void
    {
        $this->template->item = $this->actualProduct;
    }

    public function actionDetail(int $id): void
    {
        if ($id) {
            try {
                $this->actualProduct = $this->orm->products->getById($id);
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }
            if (!$this->actualProduct) {
                $this->redirect('default');
            }
        }
    }

    public function renderDetail(): void
    {
        $rootCategory = $this->orm->categories->getBy(["categoryLabel" => BasePresenter::INITIAL_CATEGORY_SPARE_PARTS]);
        $rootCategories = $this->orm->categories->getChildren($rootCategory->id);;
        $this->template->categoriesTree = $this->getCategoriesTree($rootCategories);
        $this->template->item = $this->actualProduct;
    }

    public function createComponentProductsDatagrid(string $name): ProductsDatagrid
    {
        return $this->productsDatagridFactory->create();
    }

    public function createComponentProductForm(): ProductForm
    {
        return $this->productFormFactory->create($this->actualProduct);
    }

    public function createComponentProductImagesForm(): ProductImagesForm
    {
        return $this->productImagesFormFactory->create($this->actualProduct);
    }

    public function handleDelete(int $id): void
    {
        $item = $this->orm->products->getById($id);

        if ($item) {
            $item->deleted = true;
            $this->orm->persistAndFlush($item);
        }

        if ($this->isAjax()) {
            $this['productsDatagrid']->reload();
        } else {
            $this->redirect('default');
        }
    }

    private function getCategoriesTree(Result $result): array
    {
        $categoriesIds = [];
        foreach($this->actualProduct->productCategories as $productCategory) {
            $parents = $this->orm->categories->getParents($productCategory->category->id)->fetchPairs(null, "id");
            foreach ($parents as $parent) {
                if(!in_array($parent, $categoriesIds)) {
                    $categoriesIds[] = $parent;
                }
            }
        }

        $categories = [];
        foreach ($result as $category) {
            $isInPathToActualCategory = in_array($category->id, $categoriesIds);
            $categories[] = [
                'id' => $category->id,
                'text' => $category->name,
                'parent' => $category->parent,
                'href' => $this->link('Categories:default', ['id' => $category->id]),
                'state' => [
                    'selected' => false,
                    'expanded' => $isInPathToActualCategory
                ]
            ];
        }

        $tree = [];
        foreach ($categories as $category){
            $tree[$category['parent']][] = $category;
        }
        $tree = $this->createTree($tree, array($categories[0]));
        return $tree[0]['nodes'];
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

    public function handleToggleProductToCategory()
    {
        $categoryId = $_POST['categoryId'];
        $category = $this->orm->categories->getById($categoryId);
        if ($this->checkCategory($category->id)) {
            $category = $this->orm->productCategories->getBy([
                'category->id' => $categoryId,
                'product' => $this->actualProduct
            ]);
            $this->orm->removeAndFlush($category);
        } else {
            if ($category) {
                $productCategory = new ProductCategory();
                $productCategory->product = $this->actualProduct;
                $productCategory->category = $category;
                $this->orm->persistAndFlush($productCategory);
            }
        }
        $this->getPayload()->response = "success";
        $this->sendPayload();
    }

    public function checkCategory($categoryId)
    {
        $category = $this->orm->productCategories->getBy([
            'category->id' => $categoryId,
            'product' => $this->actualProduct
        ]);
        if ($category) {
            return true;
        } else {
            return false;
        }
    }
}