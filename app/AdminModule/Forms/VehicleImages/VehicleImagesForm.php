<?php

namespace App\AdminModule\Forms;

use App\Model\Order;
use App\Model\Orm;
use App\Model\Vehicle;
use App\Model\VehicleImage;
use App\Model\Upload\UploadCarService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Tracy\Debugger;
use Zet\FileUpload\FileUploadControl;
use Zet\FileUpload\Model\DefaultFile;
use Zet\FileUpload\Template\Renderer\Bootstrap4Renderer;

interface IVehicleImagesFormFactory{
    /** @return VehicleImagesForm */
    function create(Vehicle $vehicle);
}

class VehicleImagesForm extends Control
{

    public Vehicle $vehicle;

    public Orm $orm;


    public function __construct(Vehicle $vehicle, Orm $orm)
    {
        $this->vehicle = $vehicle;
        $this->orm = $orm;
    }

    protected function createComponentVehicleImagesForm(){
        $form = new Form();
        $uploader = $form->addFileUpload("uploader");
        $uploader->setUploadModel(UploadCarService::class);
        $uploader->setParams(["vehicleId" => $this->vehicle->id]);
        $uploader->setRenderer(Bootstrap4Renderer::class);

        foreach ($this->vehicle->images->toCollection()->findBy(['size' => VehicleImage::SIZE_ORIGINAL]) as $image) {
            $file = new DefaultFile();
            $file->setPreview($image->filePath);
            $file->setFileName($image->originalName);
            $file->setIdentifier($image->id);
            $file->onDelete[] = [$this, 'deleteItem'];
        $uploader->addDefaultFile($file);
        }

        $form->addSubmit("submit", $this->vehicle ? 'Upravit auto' : 'Přidat auto');

        $form->onSuccess[] = [$this, 'editVehicleImagesFormSucceeded'];
        $form->onError[] = function($form) {
            Debugger::barDump($form->getErrors());
        };
        return $form;
    }

    public function deleteItem($imageId)
    {
        $image = $this->vehicle->images->toCollection()->getById($imageId);
        if ($image) {
            $imagesToDelete = $this->vehicle->images->toCollection()->findBy(['originalName' => $image->originalName]);
            foreach ($imagesToDelete as $imageToDelete) {
                $this->orm->remove($imageToDelete);
            }
            $this->orm->flush();
        }
    }

    public function editVehicleImagesFormSucceeded(Form $form, $values)
    {
    }

    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../Forms/VehicleImages/VehicleImagesForm.latte");
        $this->getTemplate()->render();
    }

}