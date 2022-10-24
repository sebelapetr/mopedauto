<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\IContactFormFactory;
use App\Model\Vehicle;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;

class CarsPresenter extends BasePresenter
{

    public Vehicle $vehicle;

    /** @inject  */
    public IContactFormFactory $contactFormFactory;

    public function renderDefault()
    {
        $this->getTemplate()->cars = $this->orm->vehicles->findBy(["deleted" => false]);
    }

    public function actionDetail(string $seoName)
    {
        $car = $this->orm->vehicles->getBy(["seoName" => $seoName, "deleted" => false]);
        if ($car) {
            $this->getTemplate()->car = $car;
            $this->vehicle = $car;
        }
    }

    public function createComponentContactForm(string $name): ?IComponent
    {
        return $this->contactFormFactory->create();
    }

}