<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\OrderLog;

interface IOrderFormFactory
{
    function create(): OrderForm;
}