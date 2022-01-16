<?php
declare(strict_types=1);

namespace App\AppModule\Forms;

interface IRegisterFormFactory{
    /** @return RegisterForm */
    function create();
}