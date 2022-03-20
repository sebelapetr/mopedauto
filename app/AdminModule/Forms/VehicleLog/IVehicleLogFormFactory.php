<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\VehicleLog;

interface IVehicleLogFormFactory
{
    function create(?VehicleLog $vehicleLog): VehicleLogForm;
}