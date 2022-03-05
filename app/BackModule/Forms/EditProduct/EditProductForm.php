<?php

namespace App\BackModule\Forms;

use App\Model\ProductService;
use App\Model\EditParentCategoryService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use App\Model\AddSerialService;
use Tracy\Debugger;

interface IEditProductFormFactory{
    /** @return EditProductForm */
    function create($id);
}

class EditProductForm extends Control{

    /** @var ProductService */
    public $productService;

    /** @var int */
    public $productId;


    public function __construct(ProductService $productService, $id)
    {

        $this->productService = $productService;
        $this->productId = $id;
    }

    protected function createComponentEditProductForm(){
        $form = new Form();
        $form->addHidden('id', $this->productId);
        $form->addText("name")
            ->setDefaultValue($values = $this->productService->getDefaultValues($this->productId, 'name'));
        $form->addText("normPrice")
            ->setDefaultValue($values = $this->productService->getDefaultValues($this->productId, 'normPrice'));
        $form->addText("price")
            ->setDefaultValue($values = $this->productService->getDefaultValues($this->productId, 'price'));
        $form->addText("manufacturer")
            ->setDefaultValue($values = $this->productService->getDefaultValues($this->productId, 'manufacturer'));
        $form->addText("deliveryTime")
            ->setDefaultValue($values = $this->productService->getDefaultValues($this->productId, 'deliveryTime'));
        $form->addText("weight")
            ->setDefaultValue($values = $this->productService->getDefaultValues($this->productId, 'weight'));
        $form->addText("dumbellsWeight")
            ->setDefaultValue($values = $this->productService->getDefaultValues($this->productId, 'dumbellsWeight'));
        $form->addText("length")
            ->setDefaultValue($values = $this->productService->getDefaultValues($this->productId, 'length'));
        $form->addText("width")
            ->setDefaultValue($values = $this->productService->getDefaultValues($this->productId, 'width'));
        $form->addText("height")
            ->setDefaultValue($values = $this->productService->getDefaultValues($this->productId, 'height'));
        $form->addUpload("image")
            ->setDefaultValue($values = $this->productService->getDefaultValues($this->productId, 'image'));
        $form->addTextArea("description")
            ->setDefaultValue($values = $this->productService->getDefaultValues($this->productId, 'description'));
        $form->addSelect("visible", '', ['1'=>'Ano', '0'=>'Ne']);
        $form->addSelect("repaired", '', ['0'=>'Ne', '0'=>'Ne']);
        $form->addSelect("new", '', ['1'=>'Ano', '0'=>'Ne']);
        $form->addSelect("category", '', $this->productService->getCategories())
            ->setDisabled(!$this->productService->categoriesExists());
        $form->addSubmit("submit");
        $form->onSuccess[] = [$this, 'editProductFormSucceeded'];
        return $form;
    }
    public function editProductFormSucceeded(Form $form, $values){
        $this->productService->editProduct($values);

        if  ($values->image != $this->productService->getDefaultValues($this->productId, 'image')){
            if ((filesize($values->image) > 0) and $values->image->isImage()) {
                $soubor = $values->image;
                $soubor->move("img/" . $values->image->name);
            }
    }
        $this->getPresenter()->flashMessage("Produkt byl úspěšně upraven.");
        $this->getPresenter()->redirect("Product:default");
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/EditProduct/EditProduct.latte");
        $this->getTemplate()->render();
    }

}