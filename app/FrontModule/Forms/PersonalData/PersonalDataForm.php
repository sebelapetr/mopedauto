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
        ->setRequired('Vyplňte jméno');
        $form->addText('surname')
        ->setRequired('Vyplňte příjmení');
        $form->addText('telephone')
        ->setRequired('Vyplňte telefon');
        $form->addEmail('email')
        ->setRequired('Vyplňte email');
        //--------------------------------------//
        $form->addCheckbox('companyBuy')
            ->addCondition($form::EQUAL, true)
            ->toggle('company-wrapper');
        $form->addText('company')
            ->addConditionOn( $form['companyBuy'], $form::EQUAL, TRUE )
            ->setRequired('Vyplňte název firmy');
        $form->addText('ico')
            ->addConditionOn( $form['companyBuy'], $form::EQUAL, TRUE )
            ->setRequired('Vyplňte IČO firmy');
        $form->addText('dic');
        //---------------------------------------//
        $form->addText('street')
        ->setRequired('Vyplňte ulici a č.p.');
        $form->addText('city')
        ->setRequired('Vyplňte město');
        $form->addText('psc')
        ->setRequired('Vyplňte PSČ');
        //--------------------------------------//
        $form->addCheckbox('otherAddress')
            ->addCondition($form::EQUAL, true)
            ->toggle('address-wrapper');
        $form->addText('deliveryName')
            ->addConditionOn( $form['otherAddress'], $form::EQUAL, TRUE )
            ->setRequired('Vyplňte doručovací jméno');
        $form->addText('deliverySurname')
            ->addConditionOn( $form['otherAddress'], $form::EQUAL, TRUE )
            ->setRequired('Vyplňte doručovací příjmení');
        $form->addText('deliveryCompany');
        $form->addText('deliveryStreet')
            ->addConditionOn( $form['otherAddress'], $form::EQUAL, TRUE )
            ->setRequired('Vyplňte doručovací ulici a č.p.');
        $form->addText('deliveryCity')
            ->addConditionOn( $form['otherAddress'], $form::EQUAL, TRUE )
            ->setRequired('Vyplňte doručovací město');
        $form->addText('deliveryPsc')
            ->addConditionOn( $form['otherAddress'], $form::EQUAL, TRUE )
            ->setRequired('Vyplňte doručovací PSČ');
        //--------------------------------------//
        $form->addTextArea('note');
        //-------------------------------------//
        $form->addCheckbox('newsletter');
        $form->addCheckbox('terms')
            ->setRequired('Pro odeslání objednávky je potřeba odsouhlasit obchodní podmínky.');
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