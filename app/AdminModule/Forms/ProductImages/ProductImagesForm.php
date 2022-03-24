<?php

namespace App\AdminModule\Forms;

use App\Model\Order;
use App\Model\Orm;
use App\Model\Product;
use App\Model\ProductImage;
use App\Model\ProductService;
use App\Model\Upload\UploadService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Tracy\Debugger;
use Zet\FileUpload\FileUploadControl;
use Zet\FileUpload\Model\DefaultFile;
use Zet\FileUpload\Template\Renderer\Bootstrap4Renderer;

interface IProductImagesFormFactory{
    /** @return ProductImagesForm */
    function create(Product $product);
}

class ProductImagesForm extends Control{

    /** @var ProductService */
    public $productService;

    public Product $product;

    public Orm $orm;


    public function __construct(ProductService $productService, Product $product, Orm $orm)
    {

        $this->productService = $productService;
        $this->product = $product;
        $this->orm = $orm;
    }

    protected function createComponentProductImagesForm(){
        $form = new Form();
        $uploader = $form->addFileUpload("uploader");
        $uploader->setUploadModel(UploadService::class);
        $uploader->setParams(["productId" => $this->product->id]);
        $uploader->setRenderer(Bootstrap4Renderer::class);

        foreach ($this->product->images->toCollection()->findBy(['size' => ProductImage::SIZE_ORIGINAL]) as $image) {
            $file = new DefaultFile();
            $file->setPreview($image->filePath);
            $file->setFileName($image->originalName);
            $file->setIdentifier($image->id);
            $file->onDelete[] = [$this, 'deleteItem'];
        $uploader->addDefaultFile($file);
        }

        $form->addSubmit("submit", $this->product ? 'Upravit produkt' : 'Přidat produkt');

        $form->onSuccess[] = [$this, 'editProductImagesFormSucceeded'];
        $form->onError[] = function($form) {
            Debugger::barDump($form->getErrors());
        };
        return $form;
    }

    public function deleteItem($imageId)
    {
        $image = $this->product->images->toCollection()->getById($imageId);
        if ($image) {
            $imagesToDelete = $this->product->images->toCollection()->findBy(['originalName' => $image->originalName]);
            foreach ($imagesToDelete as $imageToDelete) {
                $this->orm->remove($imageToDelete);
            }
            $this->orm->flush();
        }
    }

    public function editProductImagesFormSucceeded(Form $form, $values)
    {
    }

    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../Forms/ProductImages/ProductImagesForm.latte");
        $this->getTemplate()->render();
    }

}