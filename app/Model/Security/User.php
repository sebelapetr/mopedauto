<?php
declare(strict_types=1);

namespace App\Model\Security;

use App\Model\Orm;
use Nette\Security\IAuthenticator;
use Nette\Security\IAuthorizator;
use Nette\Security\IUserStorage;

/**
 * Class User
 * @package App\Model\Security
 * @property-read \App\Model\User $user
 */
class User extends \Nette\Security\User
{
    /** @var Orm */
    private $orm;

    /** @var User */
    private $user;

    public function __construct(Orm $orm, IUserStorage $storage, IAuthenticator $authenticator = null, IAuthorizator $authorizator = null)
    {
      $this->orm = $orm;
      parent::__construct($storage, $authenticator, $authorizator);
    }

    public function getUser(): ?\App\Model\User
    {
        if ($this->isLoggedIn()) {
            if (empty($this->user)) {
                $this->user = $this->orm->users->getById($this->id);
            }
            if (empty($this->user)) {
                $this->logout(TRUE);
            }
            return $this->user;
        }
    }

    public function isAllowed($resource = IAuthorizator::ALL, $privilege = IAuthorizator::ALL): bool
    {
        return parent::isAllowed($resource, $privilege); // TODO: Change the autogenerated stub
    }
}