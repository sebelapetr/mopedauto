<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;


interface ILoginFormFactory
{
    function create(): LoginForm;
}