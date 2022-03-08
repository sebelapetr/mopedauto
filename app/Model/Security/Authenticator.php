<?php
declare(strict_types=1);

namespace App\Model\Security;

use App\Model\Orm;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

class Authenticator implements IAuthenticator
{

    private Orm $orm;
    private Passwords $passwords;

    public function __construct(Orm $orm, Passwords $passwords)
    {
        $this->orm = $orm;
        $this->passwords = $passwords;
    }

    function authenticate(array $credentials): IIdentity
    {
        $user = $this->orm->users->getBy(['email'=>$credentials[0]]);

        if($user == NULL){
            throw new \LogicException("common.login.userNotFound");
        }

        if (!$user->active)
        {
            throw new \LogicException("common.login.userIsNotActive");
        }

        if (!$this->passwords->verify($credentials[1], $user->password)) {
            throw new \LogicException('common.login.badPassword');
        }

        $user->lastLogin = new \DateTimeImmutable();
        $this->orm->persistAndFlush($user);

        return new Identity($user->id, $user->role->intName);
    }
}