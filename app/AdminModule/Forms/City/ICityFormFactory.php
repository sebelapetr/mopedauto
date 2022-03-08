<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\City;

interface ICityFormFactory
{
    function create(?City $city): CityForm;
}