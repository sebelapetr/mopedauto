<?php

namespace App\Model;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;

/**
 * Class VehiclesRepository
 * @package App\Model
 */

class VehiclesRepository extends Repository{

    /**
     * Returns possible entity class names for current repository.
     * @return string[]
     */
    public static function getEntityClassNames(): array
    {
        return [Vehicle::class];
    }

    public function getById($id, $includeDeleted = false): ?Vehicle
    {
        $args['id'] = $id;
        if (!$includeDeleted) {
            $args['deleted'] = false;
        }
        return $this->getBy($args);
    }
}