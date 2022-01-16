<?php
declare(strict_types = 1);

namespace Model\Enum;

class VehicleBrandsEnum extends BaseEnum
{
    const BRAND_OPEL = "opel";
    const BRAND_FIAT = "fiat";
    const BRAND_VOLKSWAGEN = "volkswagen";
    const BRAND_MOTORRO = "motorro";

    public static function getConstants(): array
    {
        $reflectionClass = new \ReflectionClass(static::class);
        return $reflectionClass->getConstants();
    }

    public static function getEnum(): array
    {
        $enum = [];
        foreach (static::getConstants() as $const => $value)
        {
            $enum[$value] = $value;
        }
        return $enum;
    }
}