<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Role;

interface IEditRightsFormFactory
{
    function create(Role $role): EditRightsForm;
}