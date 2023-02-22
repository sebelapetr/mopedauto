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
 * @property string|null $seoName
 * @property string $annotation
 * @property string $description
 * @property boolean $deleted {default false}
 * @property VehicleImage[]|NULL|OneHasMany $images {1:m VehicleImage::$vehicle}             
 * 
 * #PARAMS
 * @property float $priceCzk
 * @property float $priceEur
 * @property bool|NULL $vatDeduction
 * @property string|NULL $color
 * @property int|NULL $allowedAge
 * @property string|NULL $manufactureYear
 * @property string|NULL $kilometers
 * @property string|NULL $fuel
 * 
 * @property bool|NULL $param1 {default false}
 * @property bool|NULL $param2 {default false}
 * @property bool|NULL $param3 {default false}
 * @property bool|NULL $param4 {default false}
 * @property bool|NULL $param5 {default false}
 * @property bool|NULL $param6 {default false}
 * @property bool|NULL $param7 {default false}
 * @property bool|NULL $param8 {default false}
 * @property bool|NULL $param9 {default false}
 * @property bool|NULL $param10 {default false}
 * @property bool|NULL $param11 {default false}
 * @property bool|NULL $param12 {default false}
 * @property bool|NULL $param13 {default false}
 * @property bool|NULL $param14 {default false}
 * @property bool|NULL $param15 {default false}
 * @property bool|NULL $param16 {default false}
 */
class Vehicle extends Entity
{
    public function getMainImage(string $size): ?VehicleImage
    {
        return $this->images->countStored() !== 0 ? $this->images->toCollection()->getBy(['sort' => 0, 'size' => $size]) : null;
    }

    public function getOtherImages(string $size): ICollection
    {
        return $this->images->toCollection()->findBy(['size' => $size])->limitBy(50, 1);
    }

    public function getImageOtherSize(VehicleImage $vehicleImage, string $size): ?VehicleImage
    {
        $fileName = str_replace($vehicleImage->size, $size, $vehicleImage->fileName);
        return $this->images->toCollection()->getBy(['fileName' => $fileName]);
    }
}