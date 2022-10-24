<?php

namespace App\Model;

use Nextras\Orm\Repository\Repository;

class CustomersRepository extends Repository{

    /**
     * Returns possible entity class names for current repository.
     * @return string[]
     */
    public static function getEntityClassNames(): array
    {
        return [Customer::Class];
    }

    public function getByEmail($email){
        return $this->getBy(['email' => $email]);
    }
}