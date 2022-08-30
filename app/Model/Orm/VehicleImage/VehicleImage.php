<?php

namespace App\Model;

use Nextras\Orm\Entity\Entity;
use Tracy\Debugger;

/**
 * Class VehicleImage
 * @package App\Model
 * @property int $id {primary}
 * @property string $originalName
 * @property string $fileName
 * @property string $filePath
 * @property string $size {enum self::SIZE_*}
 * @property int $sort {default 0}
 * @property Vehicle|NULL $vehicle {m:1 Vehicle::$images}
 */
class VehicleImage extends Entity
{
    public const SIZE_XS = "xs";
    public const SIZE_S = "s";
    public const SIZE_MD = "md";
    public const SIZE_LG = "lg";
    public const SIZE_ORIGINAL = "orig";
}