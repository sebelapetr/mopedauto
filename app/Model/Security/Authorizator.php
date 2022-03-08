<?php
declare(strict_types=1);

namespace App\Model;

use App\Model\Exceptions\PermissonsException;
use Nette\Security\IAuthorizator;
use Tracy\Debugger;

class Authorizator implements IAuthorizator
{
    public Orm $orm;

    function __construct(Orm  $orm)
    {
        $this->orm = $orm;
    }

    function isAllowed($role, $resource, $privilege): bool
    {
        $role = $this->orm->roles->getBy(['intName' => $role]);

        $right = $this->orm->rights->getBy(['name' => $resource]);

        if (!$right)
        {
            throw new PermissonsException('Oprávnění k modulu "'.$resource.'" nebylo nalezeno.');
        }
        $action = $this->orm->actions->getBy(['name' => $privilege, 'right' => $right]);
        if (!$action)
        {
            throw new PermissonsException('Akce "'.$privilege.'" k modulu "'.$resource.'" nebyla nalezena.');
        }

        if ($role->actions->toCollection()->getBy(['id' => $action->id]))
        {
            return true;
        } else {
            return false;
        }
    }
}