<?php

namespace App\Model;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;

/**
 * Class ProductsRepository
 * @package App\Model
 *
 * @method ICollection|Product[] insertProduct()
 * @method ICollection|Product[] insertStock()
 * @method ICollection|Product[] findProducts($phrase, $limit, $offset)
 * @method ICollection|Product[] updateAvailable()
 */

class ProductsRepository extends Repository{

    /**
     * Returns possible entity class names for current repository.
     * @return string[]
     */
    public static function getEntityClassNames(): array
    {
        return [Product::class];
    }

    public function getById($id, $includeDeleted = false): ?Product
    {
        $args['id'] = $id;
        if (!$includeDeleted) {
            $args['deleted'] = false;
        }
        return $this->getBy($args);
    }
}