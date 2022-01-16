<?php
declare(strict_types=1);

namespace App\AppModule\Forms;

use App\Model\DeliveryType;

interface IDeliveryTypeFormFactory
{
    function create(?DeliveryType $deliveryType): DeliveryTypeForm;
}