<?php

namespace App\BackModule\Forms;

use App\Model\CategoryService;
use App\Model\ProductService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Tracy\Debugger;

interface ICategoryListFormFactory{
    /** @return CategoryListForm */
    function create();
}

class CategoryListForm extends Control{

    /** @var CategoryService */
    public $categoryService;

    /** @var int */
    public $productId;

    public function __construct(CategoryService $categoryService)
    {

        $this->categoryService = $categoryService;
    }

    protected function createComponentCategoryListForm(){
        $form = new Form();
        $form->addSelect('category', '', $this->categoryService->getCategories())
            ->setDisabled(!$this->categoryService->categoriesExists());
        $form->addSubmit("submit")
            ->setDisabled(!$this->categoryService->categoriesExists());
        $form->onSuccess[] = [$this, 'categoryListFormSucceeded'];
        return $form;
    }

    public function categoryListFormSucceeded($values){
        $this->getPresenter()->redirect("Category:edit", $values['category']->value);
    }

    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/CategoryList/CategoryList.latte");
        $this->getTemplate()->render();
    }


}