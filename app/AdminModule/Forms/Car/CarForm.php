<?php

namespace App\AdminModule\Forms;

use App\Model\Orm;
use App\Model\Product;
use App\Model\Vehicle;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

interface ICarFormFactory{
    /** @return CarForm */
    function create(?Vehicle $vehicle);
}

class CarForm extends Control
{
    public ?Vehicle $car;

    public Orm $orm;


    public function __construct(?Vehicle $vehicle, Orm $orm)
    {
        $this->car = $vehicle;
        $this->orm = $orm;
    }

    protected function createComponentEditCarForm(): Form
    {
        $form = new Form();

        $form->addText("name")
            ->setRequired();

        $form->addText("priceCzk")
            ->setRequired();

        $form->addText("priceEur")
            ->setRequired();

        $form->addInteger("allowedAge")
            ->setRequired();

        $form->addText("manufactureYear")
            ->setRequired();

        $form->addText("kilometers")
            ->setRequired();

        $form->addCheckbox('vatDeduction');

        $form->addSubmit("submit", $this->car ? 'Upravit auto' : 'Přidat auto');

        if ($this->car) {
            $form->setDefaults($this->car->toArray());
        }
        $form->onSuccess[] = [$this, 'editCarFormSucceeded'];
        $form->onError[] = function($form) {
            Debugger::barDump($form->getErrors());
        };
        return $form;
    }

    public function editCarFormSucceeded(Form $form, $values)
    {
        $car = $this->car;
        if (!$car) {
            $car = new Vehicle();
        }
        $car->name = $values->name;
        $car->priceCzk = $values->priceCzk;
        $car->priceEur = $values->priceEur;
        $car->vatDeduction = $values->vatDeduction;
        $car->allowedAge = $values->allowedAge;
        $car->manufactureYear = $values->manufactureYear;
        $car->kilometers = $values->kilometers;
        $this->orm->persistAndFlush($car);

        /*
        if  ($values->image != $this->carService->getDefaultValues($this->carId, 'image')){
            if ((filesize($values->image) > 0) and $values->image->isImage()) {
                $soubor = $values->image;
                $soubor->move("img/" . $values->image->name);
            }
    }*/
        if ($this->car) {
            $this->getPresenter()->flashMessage("Auto bylo úspěšně upraven.");
        } else {
            $this->getPresenter()->flashMessage("Auto bylo úspěšně vytvořeno.");
        }
        $this->getPresenter()->redirect("Cars:detail", ['id' => $car->id]);
    }

    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../Forms/Car/CarForm.latte");
        $this->getTemplate()->render();
    }

}