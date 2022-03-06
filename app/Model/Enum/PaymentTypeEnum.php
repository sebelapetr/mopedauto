<?php
declare(strict_types = 1);

namespace App\Model\Enum;

class PaymentTypeEnum extends BaseEnum
{
    const PAYMENT_TYPE_CARD = "CARD";
    const PAYMENT_TYPE_CASH = "CASH";
    const PAYMENT_TYPE_MEAL_TICKET = "MEAL_TICKET";

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