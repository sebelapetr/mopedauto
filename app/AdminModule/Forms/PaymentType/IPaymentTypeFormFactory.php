<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\PaymentType;

interface IPaymentTypeFormFactory
{
    function create(?PaymentType $paymentType): PaymentTypeForm;
}