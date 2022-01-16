<?php

namespace App\AppModule\Forms;

use App\Model\QuoteService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class QuotesForm extends Control{

    /** @var QuoteService */
    public $quoteService;

    /** @var int */
    public $productId;

    public function __construct(QuoteService $quoteService)
    {

        $this->quoteService = $quoteService;
    }

    protected function createComponentQuotesForm(){
        $form = new Form();
        $form->addSelect('quote', '', $this->quoteService->getCategories())
            ->setDisabled(!$this->quoteService->categoriesExists());
        $form->addSubmit("submit")
            ->setDisabled(!$this->quoteService->categoriesExists());
        $form->onSuccess[] = [$this, 'quotesFormSucceeded'];
        return $form;
    }

    public function quotesFormSucceeded($values){
        $this->getPresenter()->redirect("Quote:edit", $values['quote']->value);
    }

    public function render(){
        $this->getTemplate()->setFile(__DIR__ . "/../../forms/Quotes/Quotes.latte");
        $this->getTemplate()->render();
    }


}