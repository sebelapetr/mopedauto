<?php

namespace App\BackModule\Presenters;

use App\BackModule\Forms\IEditProductFormFactory;
use App\BackModule\Forms\IProductsListFormFactory;
use App\BackModule\Forms\IAddProductFormFactory;
use App\Model\Orm;
use Tracy\Debugger;

Class ProductPresenter extends BasePresenter{


    /** @var IEditProductFormFactory */
    public  $editProductFormFactory;

    /** @var IProductsListFormFactory */
    public  $productsListFormFactory;

    /** @var IAddProductFormFactory */
    public  $addProductFormFactory;

    /** @var int */
    public $productId;


    public function __construct(Orm $orm, IEditProductFormFactory $editProductFormFactory, IProductsListFormFactory $productsListFormFactory, IAddProductFormFactory $addProductFormFactory)
    {
        parent::__construct($orm);
        $this->editProductFormFactory = $editProductFormFactory;
        $this->productsListFormFactory = $productsListFormFactory;
        $this->addProductFormFactory = $addProductFormFactory;
    }

    public function renderDefault()
    {
        $this->getTemplate()->setFile(__DIR__ . "/../templates/Product/default.latte");
        $this->getTemplate()->products = $this->orm->products->findAll();
    }

    public function renderEdit($id){
        $this->getTemplate()->product = $this->orm->products->getBy(["id"=>$id]);
        $this->productId = $id;
    }

    public function renderList()
    {
        $this->getTemplate()->setFile(__DIR__ . "/../templates/Product/list.latte");
        $this->getTemplate()->products = $this->orm->products->findAll();
    }

    public function renderAdd(){
        $this->getTemplate()->setFile(__DIR__ . "/../templates/Product/add.latte");
    }

    public function createComponentEditProductForm()
    {
        return $this->editProductFormFactory->create($this->productId);
    }

    public function createComponentProductsListForm()
    {
        return $this->productsListFormFactory->create();
    }

    public function createComponentAddProductForm()
    {
        return $this->addProductFormFactory->create();
    }

    public function handleDelete($id){
        $product = $this->orm->products->getBy(['id'=>$id]);
        $this->orm->remove($product);
        $this->orm->flush();
        $this->flashMessage("Produkt byl úspěšně smazán");
    }

}