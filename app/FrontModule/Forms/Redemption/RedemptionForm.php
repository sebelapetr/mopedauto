<?php

namespace App\FrontModule\Forms;

use App\Model\OrderService;
use App\Model\QuoteService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Tracy\Debugger;

interface IRedemptionFormFactory
{
    /** @return RedemptionForm */
    function create();
}

class RedemptionForm extends Control{

    /** @var OrderService */
    public $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    protected function createComponentRedemptionForm(){
        $form = new Form();
        $form->addText("name")
            ->setRequired();
        $form->addText("surname");
        $form->addEmail("email")
            ->setRequired();
        $form->addText("phone");


        $form->addText("brand")
            ->setRequired();
        $form->addText("year")
            ->setRequired();
        $form->addText("color")
            ->setRequired();
        $form->addText("kilometers")
            ->setRequired();
        $form->addText("price")
            ->setRequired();
        $form->addTextArea("equipment")
            ->setRequired();
        $form->addTextArea("state")
            ->setRequired();

        $form->addSubmit("submit");
        $form->onSuccess[] = [$this, 'redemptionFormSuccess'];
        return $form;
    }
    public function redemptionFormSuccess(Form $form, $values)
    {
        $this->orderService->sentRedemptionForm($values);
        $this->getPresenter()->flashMessage("Zpráva byla úspěšně odeslána.");
       // $this->getPresenter()->redirect('this');
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/Redemption/Redemption.latte");
        $this->getTemplate()->render();
    }

}