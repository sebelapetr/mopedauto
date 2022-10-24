<?php
declare(strict_types=1);

namespace App\Model\Security;

use App\Model\Customer;
use App\Model\Orm;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Tracy\Debugger;

class CustomerAuthenticator implements IAuthenticator
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
        /** @var Customer $customer */
        $customer = $this->orm->customers->getBy(['email'=>$credentials[0]]);

        if($customer == NULL){
            throw new \LogicException("Uživatel nebyl nalezen");
        }

        if (!$customer->active)
        {
            throw new \LogicException("Uživatel není aktivní.");
        }

        if (!$this->passwords->verify($credentials[1], $customer->password)) {
            throw new \LogicException('Špatně zadané heslo.');
        }

        $customer->lastLogin = new \DateTimeImmutable();
        $this->orm->persistAndFlush($customer);

        return new Identity($customer->id);
    }
}