<?php

namespace App\Model;

use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * Class ProductParameter
 * @package App\Model
 * @property int $id {primary}
 * @property string $name {enum self::NAME_*}
 * @property ProductParameterValue[]|NULL|OneHasMany $productParameterValues {1:m ProductParameterValue::$productParameter}
 */
class ProductParameter extends Entity
{
    public const NAME_GOODS_TYPE = "goods_type";
}