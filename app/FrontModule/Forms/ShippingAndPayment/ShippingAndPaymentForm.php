<?php

namespace App\FrontModule\Forms;

use App\Model\QuoteService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

interface IShippingAndPaymentFormFactory{
    /** @return ShippingAndPaymentForm */
    function create();
}

class ShippingAndPaymentForm extends Control{

    public function __construct()
    {
    }

    protected function createComponentShippingAndPaymentForm()
    {
        $form = new Form();

        $form->addRadioList('shipping', 'Způsob doručení', [2=>'Osobní odběr',1=>'PPL malý balík'])
            ->setRequired('Vyberte typ dopravy')
            ->addCondition($form::FILLED, true)
            ->toggle('payment-container')
            ->addCondition($form::EQUAL, 2)
            ->toggle('payment-type-2')
            ->elseCondition()
            ->toggle('payment-type-1');

        $form->addRadioList('payment', 'Způsob platby', [2=>'V hotovosti', 1=>'Dobírka'])
            ->setRequired('Vyberte typ platby');

        $form->addSubmit('submit', 'Pokračovat v objednávce');
        $form->onSuccess[] = [$this, 'shippingAndPaymentFormSucceeded'];

        return $form;
    }
    public function shippingAndPaymentFormSucceeded(Form $form, ArrayHash $values){
        $shippingSection = $this->getPresenter()->getSession()->getSection('shipping');
        $paymentSection = $this->getPresenter()->getSession()->getSection('payment');
        $shippingSection->shipping = $values->shipping;
        $paymentSection->payment = $values->payment;
        $this->getPresenter()->redirect('step2');

        //$this->getPresenter()->redirect("Homepage:default");
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/ShippingAndPayment/ShippingAndPayment.latte");
        $this->getTemplate()->render();
    }

}