<?php

namespace App\AppModule\Forms;

use App\Model\OrderService;
use App\Model\QuoteService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class OrdersForm extends Control{

    /** @var OrderService */
    public $orderService;

    /** @var int */
    public $productId;

    public function __construct(OrderService $orderService)
    {

        $this->orderService = $orderService;
    }

    protected function createComponentOrdersForm(){
        $form = new Form();
        $form->addSelect('quote', '', $this->orderService->getCategories())
            ->setDisabled(!$this->quoteService->categoriesExists());
        $form->addSubmit("submit")
            ->setDisabled(!$this->quoteService->categoriesExists());
        $form->onSuccess[] = [$this, 'ordersFormSucceeded'];
        return $form;
    }

    public function ordersFormSucceeded($values){
        $this->getPresenter()->redirect("Order:edit", $values['order']->value);
    }

    public function render(){
        $this->getTemplate()->setFile(__DIR__ . "/../../forms/Quotes/Quotes.latte");
        $this->getTemplate()->render();
    }


}