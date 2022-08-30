<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\IContactFormFactory;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;

class CarsPresenter extends BasePresenter
{

    /** @inject  */
    public IContactFormFactory $contactFormFactory;

    public function renderDefault()
    {
        $this->getTemplate()->cars = $this->orm->vehicles->findBy(["deleted" => false]);
    }


    public function createComponentContactForm(string $name): ?IComponent
    {
        return $this->contactFormFactory->create();
    }
}