<?php

namespace App\Model;

use Nette\Security\Passwords;
use Tracy\Debugger;

class UserRegistrationService{

    private \App\Model\Orm $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function registerUser($email, $password){
        $user = $this->orm->users->getByEmail($email);
        if ($user !== NULL){
            throw new \Exception();
        }
            $user = new User();
            $user->password = Passwords::hash($password);
            $user->email = $email;
            $this->orm->persistAndFlush($user);
    }
}