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

    protected function createComponentShippingAndPaymentForm(){
        $form = new Form();
        $form->addRadioList('shipping', 'Způsob doručení', [
            2=>'Osobní odběr na pobočce v Brně',
            1=>'Doručení na adresu přepravcem GEIS'
        ])
        ->setDefaultValue(2);
        $form->addRadioList('payment', 'Způsob platby', [2=>'Platba převodem na účet', 1=>'Dobírka GEIS'])
        ->setDefaultValue(2);
        $form->addSubmit('submit', 'Pokračovat v objednávce')
            ->setDefaultValue('aa');
        $form->onSuccess[] = [$this, 'shippingAndPaymentFormSucceeded'];
        return $form;
    }
    public function shippingAndPaymentFormSucceeded(Form $form, ArrayHash $values){
        $shippingSection = $this->getPresenter()->getSession()->getSection('shipping');
        $paymentSection = $this->getPresenter()->getSession()->getSection('payment');
        $shippingSection->shipping = $values->shipping;
        $paymentSection->payment = $values->payment;
        $this->getPresenter()->redirect('osobniUdaje');

        //$this->getPresenter()->redirect("Homepage:default");
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/ShippingAndPayment/ShippingAndPayment.latte");
        $this->getTemplate()->render();
    }

}