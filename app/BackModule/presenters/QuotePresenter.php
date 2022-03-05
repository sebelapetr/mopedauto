<?php

namespace App\BackModule\Presenters;

use App\BackModule\Forms\IQuotesFormFactory;
use App\Model\Orm;
use Tracy\Debugger;

class QuotePresenter extends BasePresenter{

    /** @var IQuotesFormFactory */
    public  $quotesFormFactory;

    /** @var int */
    public $quoteId;

    public function __construct(Orm $orm, IQuotesFormFactory $quotesFormFactory)
    {
        parent::__construct($orm);
        $this->quotesFormFactory = $quotesFormFactory;
    }

    public function renderDefault()
    {
        $this->getTemplate()->setFile(__DIR__ . "/../templates/Quote/default.latte");
        $this->getTemplate()->quotes = $this->orm->quotes->findAll()->orderBy('createdAt', 'DESC');
    }

    public function createComponentQuotesForm()
    {
        return $this->quotesFormFactory->create();
    }

    public function handleChangeState($id){
        $quote = $this->orm->quotes->getById($id);
        if($quote->state==0){
            $quote->state = 1;
        }
        else{
            $quote->state = 0;
        }
        $quote->createdAt = $quote->createdAt;
        $this->orm->persistAndFlush($quote);
    }

}