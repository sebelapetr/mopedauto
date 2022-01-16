<?php

namespace App\Model;

use Nextras\Orm\Repository\Repository;

class CategoriesRepository extends Repository{

    /**
     * Returns possible entity class names for current repository.
     * @return string[]
     */
    public static function getEntityClassNames(): array
    {
        return [Category::class];
    }

}