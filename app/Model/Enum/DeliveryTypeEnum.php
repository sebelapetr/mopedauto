<?php
declare(strict_types = 1);

namespace Model\Enum;

class DeliveryTypeEnum extends BaseEnum
{
    const DELIVERY_TYPE_SUPER_EXPRESS = "SUPER_EXPRESS";
    const DELIVERY_TYPE_EXPRESS = "EXPRESS";
    const DELIVERY_TYPE_STANDARD = "STANDARD";

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