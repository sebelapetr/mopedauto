<?php

namespace App\Model;

use Nette\Security\Passwords;
use Tracy\Debugger;

class NewsletterService
{

    private \App\Model\Orm $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function submitEmail($values)
    {
        $newsletter = new Newsletter();
        $newsletter->email = $values->email;
        $newsletter->allowed = 1;
        $newsletter->date = new \DateTime();
        $this->orm->persistAndFlush($newsletter);
    }
}