<?php

namespace App\AppModule\Forms;

use App\Model\CategoryService;
use App\Model\EditParentCategoryService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use App\Model\AddSerialService;
use Tracy\Debugger;


class AddCategoryForm extends Control{

    /** @var CategoryService */
    public $categoryService;

    /** @var int */
    public $categoryId;

    public function __construct(CategoryService $categoryService)
    {

        $this->categoryService = $categoryService;
    }

    protected function createComponentAddCategoryForm(){
        $options = $this->categoryService->getCategories();
        $options[null] = 'Rodičovská kategorie';
        $form = new Form();
        $form->addText("name");
        $form->addTextArea('description');
        $form->addSelect('visible', '', ['1'=>'Ano', '0'=>'Ne']);
        $form->addSelect('parent', '', $options);
        $form->addSubmit("submit");
        $form->onSuccess[] = [$this, 'addCategoryFormSucceeded'];
        return $form;
    }
    public function addCategoryFormSucceeded(Form $form, $values){
        $this->categoryService->addCategory($values);
        $this->getPresenter()->flashMessage("Kategorie byla úspěšně vytvořena.");

        //$this->getPresenter()->redirect("Homepage:default");
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/AddCategory/AddCategory.latte");
        $this->getTemplate()->render();
    }

}