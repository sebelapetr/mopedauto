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
 * @property Product[] $product {1:m Product::$category}
 * @property Category|NULL $parent {m:1 Category::$categories}
 * @property Category[]|NULL $categories {1:m Category::$parent}
 */

class Category extends Entity{

    public function getterUrl(): string
    {
        return $this->seoName ? $this->id . "-" . $this->seoName : $this->id;
    }

}