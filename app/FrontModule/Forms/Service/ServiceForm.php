<?php

namespace App\FrontModule\Forms;

use App\Model\OrderService;
use App\Model\QuoteService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Tracy\Debugger;

interface IServiceFormFactory
{
    /** @return ServiceForm */
    function create();
}

class ServiceForm extends Control{

    /** @var OrderService */
    public $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    protected function createComponentServiceForm(){
        $form = new Form();
        $form->addText("name")
            ->setRequired();
        $form->addText("surname");
        $form->addEmail("email")
            ->setRequired();
        $form->addText("phone");
        $form->addTextArea("message");
        $form->addSubmit("submit");
        $form->onSuccess[] = [$this, 'serviceFormSuccess'];
        return $form;
    }
    public function serviceFormSuccess(Form $form, $values)
    {
        $this->orderService->sentServiceForm($values);
        $this->getPresenter()->flashMessage("Zpráva byla úspěšně odeslána.");
        $this->getPresenter()->redirect('this');
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/Service/Service.latte");
        $this->getTemplate()->render();
    }

}