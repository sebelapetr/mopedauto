<?php
declare(strict_types=1);

namespace App\AppModule\Forms;

use App\Model\Vehicle;

interface IVehicleFormFactory
{
    function create(?Vehicle $vehicle): VehicleForm;
}