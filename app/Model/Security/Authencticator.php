<?php

namespace App\Model\Security;

use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;
use App\Model\Orm;
use Tracy\Debugger;

class Authenticator implements IAuthenticator
{

    /** @var Orm */
    private $orm;


    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    /**
     * @param array $credentials
     * @return Identity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials): Identity
    {
        # get user - depends on authentication type, requires custom exception handling
        $user = $this->orm->users->getByEmail($credentials[0]);
        if($user == NULL){
            throw new \LogicException("common.login.userNotFound");
        }
        # verify - depends on authentication type
        if (!Passwords::verify($credentials[1], $user->password)) {
            //$this->orm->loginfails->checkLoginFailsAndCreateNew($user); Nežádoucí efekt na frontendu
            throw new \LogicException('common.login.badPassword');
        }

        # login management - common (rehash password only if set)
        if (Passwords::needsRehash($user->password)) {
            $user->setPassword(Passwords::hash($credentials[1]));
        }

        $this->orm->users->persistAndFlush($user);
        # return identity - common
        return new Identity($user->id);
    }

}