<?php

namespace App\AdminModule\Forms;

use App\Model\Product;
use App\Model\ProductService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Tracy\Debugger;

interface IProductFormFactory{
    /** @return ProductForm */
    function create(?Product $product);
}

class ProductForm extends Control{

    /** @var ProductService */
    public $productService;

    public ?Product $product;


    public function __construct(ProductService $productService, ?Product $product)
    {

        $this->productService = $productService;
        $this->product = $product;
    }

    protected function createComponentEditProductForm(){
        $form = new Form();

        $form->addText("productName")
            ->setRequired();

        $form->addText("number");

        $form->addText("seoName");

        $form->addCheckbox('new');
        $form->addCheckbox('discount');
        $form->addCheckbox('isHeavy');

        $form->addCheckbox('visible');

        $form->addInteger('catalogPriceVat');

        $form->addTextArea("description");

        $form->addSubmit("submit", $this->product ? 'Upravit produkt' : 'Přidat produkt');

        if ($this->product) {
            $form->setDefaults($this->product->toArray());
        }
        $form->onSuccess[] = [$this, 'editProductFormSucceeded'];
        $form->onError[] = function($form) {
            Debugger::barDump($form->getErrors());
        };
        return $form;
    }
    public function editProductFormSucceeded(Form $form, $values)
    {
        Debugger::barDump('a');
        $product = $this->productService->editProduct($this->product, $values);

        /*
        if  ($values->image != $this->productService->getDefaultValues($this->productId, 'image')){
            if ((filesize($values->image) > 0) and $values->image->isImage()) {
                $soubor = $values->image;
                $soubor->move("img/" . $values->image->name);
            }
    }*/
        if ($this->product) {
            $this->getPresenter()->flashMessage("Produkt byl úspěšně upraven.");
        } else {
            $this->getPresenter()->flashMessage("Produkt byl úspěšně upraven.");
        }
        $this->getPresenter()->redirect("Products:detail", ['id' => $product->id]);
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/Product/ProductForm.latte");
        $this->getTemplate()->render();
    }

}