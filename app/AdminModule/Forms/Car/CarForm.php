<?php

namespace App\AdminModule\Forms;

use App\Model\Orm;
use App\Model\Product;
use App\Model\Vehicle;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;
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

        $form->addTextArea("annotation");

        $form->addTextArea("description");

        $form->addText("priceCzk")
            ->setRequired();

        $form->addText("priceEur")
            ->setRequired();

        $form->addText("color");

        $form->addText("fuel");

        $form->addInteger("allowedAge");

        $form->addText("manufactureYear");

        $form->addText("kilometers");

        $form->addCheckbox('vatDeduction');

        $form->addCheckbox('param1');
        $form->addCheckbox('param2');
        $form->addCheckbox('param3');
        $form->addCheckbox('param4');
        $form->addCheckbox('param5');
        $form->addCheckbox('param6');
        $form->addCheckbox('param7');
        $form->addCheckbox('param8');
        $form->addCheckbox('param9');
        $form->addCheckbox('param10');
        $form->addCheckbox('param11');
        $form->addCheckbox('param12');
        $form->addCheckbox('param13');
        $form->addCheckbox('param14');
        $form->addCheckbox('param15');
        $form->addCheckbox('param16');

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

    public function editCarFormSucceeded(Form $form, ArrayHash $values)
    {
        $car = $this->car;
        if (!$car) {
            $car = new Vehicle();
        }
        $car->name = $values->name;
        $car->annotation = $values->annotation;
        $car->description = $values->description;
        $car->priceCzk = $values->priceCzk;
        $car->priceEur = $values->priceEur;
        $car->vatDeduction = $values->vatDeduction;
        $car->allowedAge = $values->allowedAge;
        $car->manufactureYear = $values->manufactureYear;
        $car->kilometers = $values->kilometers;
        $car->color = $values->color;
        $car->fuel = $values->fuel;

        foreach ($values as $key => $val) {
            if (substr( $key, 0, 5 ) === 'param') {
                $car->$key = $val;
            }
        }

        $this->orm->persistAndFlush($car);
        $car->seoName = Strings::webalize($car->name)."-".$car->id;
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