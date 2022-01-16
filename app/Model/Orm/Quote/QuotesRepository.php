<?php

namespace App\Model;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;

/**
 * Class QuotesRepository
 * @package App\Model
 *
 * @method ICollection|Quote[] findCategoriesNew()
 */

class QuotesRepository extends Repository{

    /**
     * Returns possible entity class names for current repository.
     * @return string[]
     */
    public static function getEntityClassNames(): array
    {
        return [Quote::class];
    }
}