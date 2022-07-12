<?php

namespace App\Model;

use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\ManyHasMany;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * Class ProductParameterValue
 * @package App\Model
 * @property int $id {primary}
 * @property string $value
 * @property ProductParameter $productParameter {m:1 ProductParameter::$productParameterValues}
 * @property ManyHasMany|Product $products {m:m Product::$parameterValues}
 * @property OneHasMany|Category[]|NULL $categories {1:m Category::$productParameterValue}
 */
class ProductParameterValue extends Entity
{

}