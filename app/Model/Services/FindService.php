<?php

namespace App\Model;

use Nette\Security\Passwords;
use Tracy\Debugger;

class FindService
{

    private \App\Model\Orm $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }
}