<?php

namespace App\Model;

use Nette\Security\Passwords;
use Tracy\Debugger;

class QuoteService
{

    /** @var Orm */
    private $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function newQuote($values)
    {
        if (!isset($values->product)) {
            $values->product = null;
        }
        else{
            $product = $this->orm->products->getById($values->product);
        }
        $quote = new Quote();
        $quote->name = $values->name;
        $quote->surname = $values->surname;
        $quote->email = $values->email;
        $quote->phone = $values->phone;
        $quote->text = $values->message;
        $quote->product = $product;
        $quote->createdAt = date("Y-m-d h:i:sa");
        $this->orm->persistAndFlush($quote);
    }
}