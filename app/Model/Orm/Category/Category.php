<?php

namespace App\Model;
use Nextras\Orm\Entity\Entity;

/**
 * Class Category
 * @package App\Model
 * @property int $id {primary}
 * @property string $categoryName
 * @property string $categoryLabel
 * @property string|NULL $seoName
 * @property string|NULL $description
 * @property string $url {virtual}
 * @property bool $visible
 * @property Product[]|NULL $productCategories {1:m ProductCategory::$category}
 * @property Category|NULL $parent {m:1 Category::$categories}
 * @property Category[]|NULL $categories {1:m Category::$parent}
 * @property ProductParameterValue|NULL $productParameterValue {m:1 ProductParameterValue::$categories}
 */

class Category extends Entity{

    public function getterUrl(): string
    {
        return $this->seoName ? $this->id . "-" . $this->seoName : $this->id;
    }

}