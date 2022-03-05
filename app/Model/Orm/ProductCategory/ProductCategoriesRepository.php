<?php

namespace App\Model;

use Nextras\Dbal\Result\Result;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;

/**
 * Class ProductCategoriesRepository
 * @package App\Model
 *
 * @method Result getProductsInCategories(array $categories)
 *
 */

class ProductCategoriesRepository extends Repository
{

    /**
     * Returns possible entity class names for current repository.
     * @return string[]
     */
    public static function getEntityClassNames(): array
    {
        return [ProductCategory::class];
    }
}