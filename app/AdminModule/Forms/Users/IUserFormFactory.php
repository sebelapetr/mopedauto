<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\User;

interface IUserFormFactory
{
    function create(?User $user): UserForm;
}