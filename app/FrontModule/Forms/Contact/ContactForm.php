<?php

namespace App\FrontModule\Forms;

use App\Model\OrderService;
use App\Model\QuoteService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Tracy\Debugger;

interface IContactFormFactory{
    /** @return ContactForm */
    function create();
}

class ContactForm extends Control{

    /** @var OrderService */
    public $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    protected function createComponentContactForm(){
        $form = new Form();
        $form->addText("name")
            ->setRequired();
        $form->addText("surname");
        $form->addEmail("email")
            ->setRequired();
        $form->addText("phone");
        $form->addTextArea("message");
        $form->addSubmit("submit");
        $form->onSuccess[] = [$this, 'contactFormSuccess'];
        return $form;
    }
    public function contactFormSuccess(Form $form, $values)
    {
        Debugger::barDump($values);
        $this->orderService->sentContactForm($values);
        $this->getPresenter()->flashMessage("Zpráva byla úspěšně odeslána.");
        //$this->getPresenter()->redirect('this');
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/Contact/Contact.latte");
        $this->getTemplate()->render();
    }

}