<?php
declare(strict_types = 1);

namespace App\Model\Enum;

class SettingsEnum extends BaseEnum
{
    public const OPTION_MAINTENANCE_MODE = "MAINTENANCE_MODE";
    public const OPTION_CONTACT_PHONE_COURIER_SUPPORT = "CONTACT_PHONE_COURIER_SUPPORT";
    public const OPTION_CONTACT_EMAIL_COURIER_SUPPORT = "CONTACT_EMAIL_COURIER_SUPPORT";
}