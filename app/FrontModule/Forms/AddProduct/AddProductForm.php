<?php

namespace App\FrontModule\Forms;

use App\Model\AddProductService;
use App\Model\Product;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

interface IAddProductFormFactory{
    /** @return AddProductForm */
    function create(Product $product);
}

class AddProductForm extends Control
{
    public AddProductService $addProductService;

    private Product $product;

    public function __construct(AddProductService $addProductService, Product $product)
    {
        $this->addProductService = $addProductService;
        $this->product = $product;
    }

    protected function createComponentAddProductForm()
    {
        $form = new Form();
        $form->addHidden("id", $this->product->id);
        $form->addInteger('quantity');
        $form->addSubmit("submit");
        $form->onSuccess[] = [$this, 'addProductFormSucceeded'];
        return $form;
    }
    public function addProductFormSucceeded(Form $form, $values)
    {
        $this->addProductService->addProduct($values);
        $this->getPresenter()->flashMessage("Produkt byl přidán do košíku.");
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/AddProduct/AddProduct.latte");
        $this->getTemplate()->render();
    }

}