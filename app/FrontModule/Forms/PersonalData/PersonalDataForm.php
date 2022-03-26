<?php

namespace App\FrontModule\Forms;

use App\Model\ComgatePayment;
use App\Model\Order;
use App\Model\OrderService;
use App\Model\Orm;
use App\Model\QuoteService;
use App\Model\Services\ComgateService;
use App\Model\Session\CartService;
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
    public CartService $cartService;

    public Orm $orm;

    public ComgateService $comgateService;


    public function __construct(OrderService $orderService, CartService $cartService, Orm $orm, ComgateService $comgateService)
    {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
        $this->orm = $orm;
        $this->comgateService = $comgateService;
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
        $form->addSubmit('submit', 'Odeslat objednávku')
            ->setHtmlAttribute("id", "submitPersonalDataForm");
        $form->onSuccess[] = [$this, 'personalDataFormSucceeded'];
        return $form;
    }
    public function personalDataFormSucceeded(Form $form, ArrayHash $values){

        $order = $this->orderService->saveOrder($this->cartService->getOrder(), $values);

        if($order->typePayment === Order::TYPE_PAYMENT_CARD){
            if($order->comgatePayment){
                if($order->comgatePayment->status === ComgatePayment::STATE_PAID){
                    $this->presenter->flashMessage('Tato objednávka již byla zaplacena kartou');
                    $this->presenter->redirect('this');
                    return;
                }

                $this->orm->remove($order->comgatePayment);
            }
            $payment = $this->comgateService->createPayment($order);

            if(!$payment->isOk()){
                Debugger::log($payment, 'comgate-error');
                $this->presenter->flashMessage('Nepodařilo se vytvořit platbu kartou. Zkuste to prosím znovu nebo zvolte jinou platební metodu.');
                $this->presenter->redirect('this');
            }

            $paymentData = $payment->getData();
            if($paymentData && $paymentData['redirect']){
                $this->comgateService->logPayment($paymentData['transId'], $order);

                header("Location: " . $paymentData['redirect']);
                exit();
            }
        }else{
            $order->state = Order::ORDER_STATE_RECEIVED;
            $this->orm->persistAndFlush($order);
            $orderSent = $this->orderService->sendMails($order);
            if ($orderSent)
            {
                $this->cartService->reset();
                $shippingSection = $this->getPresenter()->getSession()->getSection('shipping');
                unset($shippingSection->shipping);
                $paymentSection = $this->getPresenter()->getSession()->getSection('payment');
                unset($paymentSection->payment);
                $this->getPresenter()->redirect('step3', base64_encode($order->id).'8452');
            }
        }
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__ . "/../../forms/PersonalData/PersonalData.latte");
        $this->getTemplate()->render();
    }

}