<?php

namespace App\Model;

use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\HasMany;
use Nextras\Orm\Relationships\ManyHasMany;

/**
 * Role
 * @property int $id {primary}
 * @property string $intName
 * @property HasMany|User[] $users {1:m User::$role}
 * @property ManyHasMany|Action $actions {m:m Action::$roles, isMain=TRUE}
 */
class Role extends Entity
{
    const INT_NAME_ADMIN = "ADMIN";
    const INT_NAME_OPERATOR = "OPERATOR";
    const INT_NAME_COURIER = "COURIER";
    const INT_NAME_PARTNER = "PARTNER";
    const INT_NAME_BRANCH = "BRANCH";
    const INT_NAME_DEVELOPER = "DEVELOPER";
}
