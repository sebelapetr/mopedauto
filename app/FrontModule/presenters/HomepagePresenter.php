<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\IContactFormFactory;
use App\Model\Orm;
use Nette\ComponentModel\IComponent;

Class HomepagePresenter extends BasePresenter
{
    /** @inject  */
    public IContactFormFactory $contactFormFactory;

    public function renderDefault(){
        $this->getTemplate()->setFile(__DIR__ . "/../templates/Homepage/default.latte");
    }

    public function createComponentContactForm(string $name): ?IComponent
    {
        return $this->contactFormFactory->create();
    }
}