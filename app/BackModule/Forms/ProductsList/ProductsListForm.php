<?php

namespace App\AdminModule\Forms;

use App\Model\ProductService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Tracy\Debugger;

interface IProductsListFormFactory{
    /** @return ProductsListForm */
    function create();
}

class ProductsListForm extends Control{

    /** @var ProductService */
    public $productService;

    /** @var int */
    public $productId;

    public function __construct(ProductService $productService)
    {

        $this->productService = $productService;
    }

    protected function createComponentProductsListForm(){
        $form = new Form();
        $form->addSelect('product', '', $this->productService->getProducts())
            ->setDisabled(!$this->productService->productsExists());
        $form->addSubmit("submit")
            ->setDisabled(!$this->productService->productsExists());
        $form->onSuccess[] = [$this, 'productsListFormSucceeded'];
        return $form;
    }

    public function productsListFormSucceeded($values){
            $this->getPresenter()->redirect("Product:edit", $values['product']->value);
    }

    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/ProductsList/ProductsList.latte");
        $this->getTemplate()->render();
    }


}