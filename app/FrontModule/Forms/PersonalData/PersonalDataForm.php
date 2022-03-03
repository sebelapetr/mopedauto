<?php

namespace App\FrontModule\Forms;

use App\Model\Order;
use App\Model\OrderService;
use App\Model\QuoteService;
use App\Model\Session\CartSession;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Tracy\Debugger;

interface IPersonalDataFormFactory{
    /** @return PersonalDataForm */
    function create();
}

class PersonalDataForm extends Control{

    /** @var OrderService */
    public $orderService;

    /** @inject */
    public CartSession $cartSession;

    public function __construct(OrderService $orderService, CartSession $cartSession)
    {
        $this->orderService = $orderService;
        $this->cartSession = $cartSession;
    }

    protected function createComponentPersonalDataForm(){
        $form = new Form();
        $form->addText('name')
        ->setRequired();
        $form->addText('surname')
        ->setRequired();
        $form->addText('telephone')
        ->setRequired();
        $form->addEmail('email')
        ->setRequired();
        //--------------------------------------//
        $form->addText('company');
        $form->addText('ico');
        $form->addText('dic');
        //---------------------------------------//
        $form->addText('street')
        ->setRequired();
        $form->addText('city')
        ->setRequired();
        $form->addText('psc')
        ->setRequired();
        //--------------------------------------//
        $form->addText('deliveryName');
        $form->addText('deliverySurname');
        $form->addText('deliveryCompany');
        $form->addText('deliveryStreet');
        $form->addText('deliveryCity');
        $form->addText('deliveryPsc');
        //--------------------------------------//
        $form->addTextArea('note');
        //-------------------------------------//
        $form->addCheckbox('newsletter');
        $form->addCheckbox('terms');
        $form->addSubmit('submit', 'Odeslat objednávku');
        $form->onSuccess[] = [$this, 'personalDataFormSucceeded'];
        return $form;
    }
    public function personalDataFormSucceeded(Form $form, ArrayHash $values){
        $sessionProducts = $this->cartSession->getProducts();
        $sessionShipping = $this->getPresenter()->getSession('shipping');
        $sessionPayment = $this->getPresenter()->getSession('payment');
        $sessionOrder = $this->getPresenter()->getSession('order');
        $this->orderService->newOrder($values, $sessionProducts, $sessionShipping, $sessionPayment, $sessionOrder);
        $productsSection = $this->cartSession->reset();
        $shippingSection = $this->getPresenter()->getSession()->getSection('shipping');
        unset($shippingSection->shipping);
        $paymentSection = $this->getPresenter()->getSession()->getSection('payment');
        unset($paymentSection->payment);
        $this->getPresenter()->redirect('step3', base64_encode($sessionOrder->order).'8452');
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__ . "/../../forms/PersonalData/PersonalData.latte");
        $this->getTemplate()->render();
    }

}