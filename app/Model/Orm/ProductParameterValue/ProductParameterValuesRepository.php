<?php

namespace App\Model;

use Nextras\Dbal\Result\Result;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;

/**
 * Class ProductParameterValuesRepository
 * @package App\Model
 *
 *
 */

class ProductParameterValuesRepository extends Repository
{

    /**
     * Returns possible entity class names for current repository.
     * @return string[]
     */
    public static function getEntityClassNames(): array
    {
        return [ProductParameterValue::class];
    }
}