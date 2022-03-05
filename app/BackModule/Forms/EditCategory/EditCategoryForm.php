<?php

namespace App\BackModule\Forms;

use App\Model\CategoryService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use App\Model\AddSerialService;
use Tracy\Debugger;

interface IEditCategoryFormFactory{
    /** @return EditCategoryForm */
    function create($id);
}

class EditCategoryForm extends Control{

    /** @var CategoryService */
    public $categoryService;

    /** @var int */
    public $categoryId;

    public function __construct(CategoryService $categoryService, $id)
    {

        $this->categoryService = $categoryService;
        $this->categoryId = $id;
    }

    protected function createComponentEditCategoryForm(){
        $form = new Form();
        $form->addHidden('id', $this->categoryId);
        $form->addText("name")
            ->setDefaultValue($values = $this->categoryService->getDefaultValues($this->categoryId, 'name'));;
        $form->addTextArea('description')
            ->setDefaultValue($values = $this->categoryService->getDefaultValues($this->categoryId, 'description'));;
        $form->addSelect('visible', '', ['1'=>'Ano', '0'=>'Ne']);
        $form->addSubmit("submit");
        $form->onSuccess[] = [$this, 'editCategoryFormSucceeded'];
        return $form;
    }
    public function editCategoryFormSucceeded(Form $form, $values){
        $this->categoryService->editCategory($values);
        $this->getPresenter()->flashMessage("Kategorie úspěšně aktualizována.");

        $this->getPresenter()->redirect("Category:default");
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/EditCategory/EditCategory.latte");
        $this->getTemplate()->render();
    }

}