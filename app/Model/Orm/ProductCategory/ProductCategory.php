<?php

namespace App\Model;

use Nextras\Orm\Entity\Entity;

/**
 * Class ProductCategory
 * @package App\Model
 * @property int $id {primary}
 * @property Category $category {m:1 Category::$productCategories}
 * @property Product $product {m:1 Product::$productCategories}
 */
class ProductCategory extends Entity
{
}