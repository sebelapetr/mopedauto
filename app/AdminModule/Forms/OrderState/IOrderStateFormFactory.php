<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Order;

interface IOrderStateFormFactory
{
    function create(Order $order): OrderStateForm;
}