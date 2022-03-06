<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\ContactForm;
use App\FrontModule\Forms\IContactFormFactory;
use App\FrontModule\Forms\IServiceFormFactory;
use App\FrontModule\Forms\IRedemptionFormFactory;
use App\FrontModule\Forms\RedemptionForm;
use App\FrontModule\Forms\ServiceForm;

class PagesPresenter extends BasePresenter
{

    /** @inject */
    public IContactFormFactory $contactFormFactory;

    /** @inject */
    public IServiceFormFactory $serviceFormFactory;

    /** @inject */
    public IRedemptionFormFactory $redemptionFormFactory;

    public function renderDefault()
    {

    }

    public function createComponentContactForm(): ContactForm
    {
        return $this->contactFormFactory->create();
    }

    public function createComponentRedemptionForm(): RedemptionForm
    {
        return $this->redemptionFormFactory->create();
    }

    public function createComponentServiceForm(): ServiceForm
    {
        return $this->serviceFormFactory->create();
    }

}