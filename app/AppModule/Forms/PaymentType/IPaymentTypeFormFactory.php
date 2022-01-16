<?php
declare(strict_types=1);

namespace App\AppModule\Forms;

use App\Model\PaymentType;

interface IPaymentTypeFormFactory
{
    function create(?PaymentType $paymentType): PaymentTypeForm;
}