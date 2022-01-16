<?php

namespace App\Model;

use Nette\Security\Passwords;
use Tracy\Debugger;

class FindService
{

    /** @var Orm */
    private $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }
}