<?php

namespace App\Model;

use Nette\Application\LinkGenerator;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\ManyHasMany;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * Class Vehicle
 * @package App\Model
 * @property int $id {primary}
 * @property string $name
 * @property float $priceCzk
 * @property float $priceEur
 * @property bool|NULL $vatDeduction
 * @property int|NULL $allowedAge
 * @property string|NULL $manufactureYear
 * @property string|NULL $kilometers
 * @property boolean $deleted {default false}
 * @property VehicleImage[]|NULL|OneHasMany $images {1:m VehicleImage::$vehicle}
 */
class Vehicle extends Entity
{
    public function getMainImage(string $size): ?VehicleImage
    {
        return $this->images->countStored() !== 0 ? $this->images->toCollection()->getBy(['sort' => 0, 'size' => $size]) : null;
    }
}