<?php

namespace App\FrontModule\Forms;

use App\Model\AddProductService;
use App\Model\QuoteService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Tracy\Debugger;

interface IAddProductFormFactory{
    /** @return AddProductForm */
    function create($id);
}

class AddProductForm extends Control{

    /** @var AddProductService */
    public $addProductService;

    /** @var int  */
    private $productId;

    public function __construct(AddProductService $addProductService, $id)
    {
        $this->addProductService = $addProductService;
        $this->productId = $id;
    }

    protected function createComponentAddProductForm(){
        $form = new Form();
        $form->addHidden("id", $this->productId);
        $form->addInteger('quantity');
        $form->addSubmit("submit");
        $form->onSuccess[] = [$this, 'addProductFormSucceeded'];
        return $form;
    }
    public function addProductFormSucceeded(Form $form, $values){

        $this->addProductService->addProduct($values, $this->getPresenter()->getSession()->getSection('products'));
        $this->getPresenter()->flashMessage("Produkt byl přidán do košíku.");

        //$this->getPresenter()->redirect("Homepage:default");
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/AddProduct/AddProduct.latte");
        $this->getTemplate()->render();
    }

}