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

    private Passwords $passwords;


    public function __construct(Orm $orm, Passwords $passwords)
    {
        $this->orm = $orm;
        $this->passwords = $passwords;
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
        if (!$this->passwords->verify($credentials[1], $user->password)) {
            //$this->orm->loginfails->checkLoginFailsAndCreateNew($user); Nežádoucí efekt na frontendu
            throw new \LogicException('common.login.badPassword');
        }

        # login management - common (rehash password only if set)
        if ($this->passwords->needsRehash($user->password)) {
            $user->setPassword($this->passwords->hash($credentials[1]));
        }

        $this->orm->users->persistAndFlush($user);
        # return identity - common
        return new Identity($user->id);
    }

}