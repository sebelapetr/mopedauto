<?php

namespace App\Model;

use Nette\Utils\DateTime;
use Nextras\Dbal\Utils\DateTimeImmutable;
use Nextras\Orm\Entity\Entity;

/**
 * Class Order
 * @package App\Model
 * @property int $id {primary}
 * @property string $name
 * @property string $surname
 * @property string $telephone
 * @property string $email
 * @property string $street
 * @property string $city
 * @property string $psc
 * @property string $note
 * @property string|NULL $company
 * @property string|NULL $ico
 * @property string|NULL $dic
 * @property string|NULL $deliveryName
 * @property string|NULL $deliverySurname
 * @property string|NULL $deliveryCompany
 * @property string|NULL $deliveryStreet
 * @property string|NULL $deliveryCity
 * @property string|NULL $deliveryPsc
 * @property int|NULL $newsletter
 * @property float $totalPrice
 * @property float $totalPriceVat
 * @property DateTimeImmutable|null $createdAt
 * @property int $state {default 0}
 * @property int|NULL $variableSymbol
 * @property OrdersItem[] $ordersItems {1:m OrdersItem::$order}
 * @property string|NULL $invoice
 * @property int|NULL $typePayment
 */
Class Order extends Entity{

}