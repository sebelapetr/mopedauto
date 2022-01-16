<?php

namespace App\Model;

/**
 * Class Category
 * @package App\Model
 * @property int $id {primary}
 * @property string $categoryName
 * @property string $categorySingleName
 * @property string $categoryLabel
 * @property string|NULL $description
 * @property Product[] $product {1:m Product::$category}
 * @property CategoryParent[] $category {1:m CategoryParent::$category}
 * @property CategoryParent[]|NULL $parent {1:m CategoryParent::$parent}
 */
use Nextras\Orm\Entity\Entity;

class Category extends Entity{
}