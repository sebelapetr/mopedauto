<?php
declare(strict_types=1);

namespace App\AppModule\Forms;

use App\Model\City;

interface ICityFormFactory
{
    function create(?City $city): CityForm;
}