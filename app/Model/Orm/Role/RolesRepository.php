<?php

namespace App\Model;

use Nextras\Orm\Repository\Repository;
    
class RolesRepository extends Repository
{

    public static function getEntityClassNames(): array
    {
        return [Role::class];
    }

}