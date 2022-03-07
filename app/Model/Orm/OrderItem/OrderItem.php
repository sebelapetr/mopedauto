<?php

namespace App\Model;

use Nette\Utils\DateTime;
use Nextras\Dbal\Utils\DateTimeImmutable;
use Nextras\Orm\Entity\Entity;

/**
 * Class OrdersItem
 * @package App\Model
 * @property int $id {primary}
 * @property string $type {enum self::TYPE_*}
 * @property string $name
 * @property float $price
 * @property float $priceVat
 * @property int $quantity
 * @property int $vat
 * @property DateTimeImmutable|NULL $createdAt
 * @property int|NULL $categoryType
 * @property Product|NULL $product {m:1 Product::$orderItem}
 * @property Order|NULL $order {m:1 Order::$ordersItems}

 */
class OrdersItem extends Entity
{
    public const TYPE_SHIPPING = "SHIPPING";
    public const TYPE_PAYMENT = "PAYMENT";
    public const TYPE_PRODUCT = "PRODUCT";
}