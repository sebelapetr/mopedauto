<?php
declare(strict_types = 1);

namespace Model\Enum;

class ColorsEnum extends BaseEnum
{
    const COLOR_RED = "red";
    const COLOR_WHITE = "white";

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