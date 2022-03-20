<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

interface ISettingsFormFactory
{
    function create(): SettingsForm;
}