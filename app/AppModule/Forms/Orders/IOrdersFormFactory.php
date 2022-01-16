<?php
declare(strict_types=1);

namespace App\AppModule\Forms;


interface IOrdersFormFactory
{
    function create(): OrdersForm;
}