<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:21
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Datagrids\DocumentsDatagrid;
use App\AdminModule\Components\Datagrids\ProductsDatagrid;
use App\AdminModule\Components\DocumentsDatagridFactory;
use App\AdminModule\Components\ProductsDatagridFactory;
use App\AdminModule\Forms\CategoryForm;
use App\AdminModule\Forms\ICategoryFormFactory;
use App\FrontModule\Presenters\BasePresenter;
use App\Model\Category;
use Nette\Utils\Random;
use Nette\Utils\Strings;
use Nextras\Dbal\Result\Result;
use Tracy\Debugger;

class CategoriesPresenter extends BaseAppPresenter
{
    /** @inject */
    public ProductsDatagridFactory $productsDatagridFactory;

    /** @inject */
    public DocumentsDatagridFactory $documentsDatagridFactory;

    /** @inject */
    public ICategoryFormFactory $categoryFormFactory;

    public ?Category $actualCategory;

    public function actionDefault(int $id = null): void
    {
        if ($id) {
            $this->actualCategory = $this->orm->categories->getById($id);
        } else {
            $this->actualCategory = null;
        }
        $rootCategory = $this->orm->categories->getBy(["categoryLabel" => BasePresenter::INITIAL_CATEGORY_SPARE_PARTS]);
        $rootCategories = $this->orm->categories->getChildren($rootCategory->id);;
        $this->template->categoriesTree = $this->getCategoriesTree($rootCategories);
        $this->template->actualCategory = $this->actualCategory;
    }

    private function getCategoriesTree(Result $result): array
    {
        $categories = [];
        foreach ($result as $category) {
            $isActualCategory = $this->actualCategory !== null ? $this->actualCategory->id === $category->id : null;
            $isInPathToActualCategory = $isActualCategory !== null ? in_array($this->actualCategory->id, $this->orm->categories->getChildren($category->id)->fetchPairs(null, "id")) : false;
            $categories[] = [
                'id' => $category->id,
                'text' => $category->name,
                'parent' => $category->parent,
                'href' => $this->link('Categories:default', ['id' => $category->id]),
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

    public function createComponentCategoryForm(): CategoryForm
    {
        return $this->categoryFormFactory->create($this->actualCategory);
    }

    private function removeChildCategories(Category $category): void
    {
        foreach ($category->categories as $chCategory) {
            if ($chCategory->categories) {
                $this->removeChildCategories($chCategory);
            }
            $this->orm->remove($chCategory);
        }
    }

    public function handleDelete(int $id): void
    {
        $category = $this->orm->categories->getById($id);
        foreach ($category->categories as $chCategory) {
            if ($chCategory->categories) {
                $this->removeChildCategories($chCategory);
            }
            $this->orm->remove($chCategory);
        }
        $this->orm->removeAndFlush($category);
        if ($this->isAjax()) {
        } else {
            $this->redirect('this', ['id' => null]);
        }
    }

    public function handleAddCategory(int $id): void
    {
        $parentCategory = $this->orm->categories->getById($id);

        $category = new Category();
        $rand = Random::generate(5);
        $category->categoryName = "Nová kategorie " . $rand;
        $category->categoryLabel = Strings::webalize("Nová kategorie");
        $category->seoName = "nova-kategorie-$rand";
        $category->visible = false;
        $category->parent = $parentCategory;
        $this->orm->persistAndFlush($category);
        $this->redirect('this', $category->id);
    }
}