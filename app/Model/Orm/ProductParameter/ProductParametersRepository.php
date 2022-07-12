<?php

namespace App\Model;

use Nextras\Dbal\Result\Result;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;

/**
 * Class ProductParametersRepository
 * @package App\Model
 *
 *
 */

class ProductParametersRepository extends Repository
{

    /**
     * Returns possible entity class names for current repository.
     * @return string[]
     */
    public static function getEntityClassNames(): array
    {
        return [ProductParameter::class];
    }
}