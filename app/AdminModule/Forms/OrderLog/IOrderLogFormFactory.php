<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\OrderLog;

interface IOrderLogFormFactory
{
    function create(?OrderLog $orderLog): OrderLogForm;
}