<?php

namespace App\Model;

use Nette\Utils\DateTime;
use Nextras\Dbal\Utils\DateTimeImmutable;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\OneHasMany;
use Nextras\Orm\Relationships\OneHasOne;

/**
 * Class Order
 * @package App\Model
 * @property int $id {primary}
 * @property string $hash
 * @property string|NULL $name
 * @property string|NULL $surname
 * @property string|NULL $telephone
 * @property string|NULL $email
 * @property string|NULL $street
 * @property string|NULL $city
 * @property string|NULL $psc
 * @property string|NULL $note
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
 * @property float $totalPrice {default 0}
 * @property float $totalPriceVat {default 0}
 * @property DateTimeImmutable|null $createdAt {default now}
 * @property string|NULL $state {enum self::ORDER_STATE_*} {default self::ORDER_STATE_UNFINISHED}
 * @property int|NULL $variableSymbol
 * @property OrdersItem[]|OneHasMany $ordersItems {1:m OrdersItem::$order}
 * @property string|NULL $invoice
 * @property string|NULL $typePayment {enum self::TYPE_PAYMENT_*}
 * @property string|NULL $typeDelivery {enum self::TYPE_DELIVERY_*}
 * @property string|NULL $paymentState {enum self::PAYMENT_STATE_*} {default self::PAYMENT_STATE_NOT_PAID}
 * @property OneHasOne|ComgatePayment|NULL $comgatePayment {1:1 ComgatePayment::$order, isMain=true}
 */
Class Order extends Entity
{
    public const TYPE_PAYMENT_CASH_ON_DELIVERY = "CASH_ON_DELIVERY";
    public const TYPE_PAYMENT_CARD = "CARD";
    public const TYPE_PAYMENT_CASH = "CASH";

    public const TYPE_PAYMENT_CASH_ON_DELIVERY_PRICE = 0;
    public const TYPE_PAYMENT_CARD_PRICE = 0;
    public const TYPE_PAYMENT_CASH_PRICE = 0;

    public const TYPE_DELIVERY_ADDRESS = "ADDRESS";
    public const TYPE_DELIVERY_ADDRESS_BIG = "ADDRESS_BIG";
    public const TYPE_DELIVERY_PERSONAL = "PERSONAL";

    public const TYPE_DELIVERY_ADDRESS_PRICE = 161;
    public const TYPE_DELIVERY_ADDRESS_PRICE_BIG = 237;
    public const TYPE_DELIVERY_PERSONAL_PRICE = 0;

    const ORDER_STATE_UNFINISHED = "UNFINISHED";
    const ORDER_STATE_CREATED = "CREATED";
    const ORDER_STATE_RECEIVED = "RECEIVED";
    const ORDER_STATE_COMPLETE = "COMPLETE";
    const ORDER_STATE_STORNO = "STORNO";

    const PAYMENT_STATE_NOT_PAID = "NOT_PAID";
    const PAYMENT_STATE_PAID = "PAID";

    public function getShippingPrice(bool $withVat): float
    {
        $priceWithVat = 0;
        switch($this->typeDelivery)
        {
            case self::TYPE_DELIVERY_PERSONAL:
                $priceWithVat = self::TYPE_DELIVERY_PERSONAL_PRICE;
                break;
            case self::TYPE_DELIVERY_ADDRESS:
                $priceWithVat = self::TYPE_DELIVERY_ADDRESS_PRICE;
                break;
            case self::TYPE_DELIVERY_ADDRESS_BIG:
                $priceWithVat = self::TYPE_DELIVERY_ADDRESS_PRICE_BIG;
                break;
        }
        if (!$withVat) {
            return $priceWithVat / 1.21;
        } else {
            return $priceWithVat;
        }
    }

    public function getPaymentPrice(bool $withVat): float
    {
        $priceWithVat = 0;
        switch($this->typePayment)
        {
            case self::TYPE_PAYMENT_CARD:
                $priceWithVat = self::TYPE_PAYMENT_CARD_PRICE;
                break;
            case self::TYPE_PAYMENT_CASH:
                $priceWithVat = self::TYPE_PAYMENT_CASH_PRICE;
                break;
            case self::TYPE_PAYMENT_CASH_ON_DELIVERY:
                $priceWithVat = self::TYPE_PAYMENT_CASH_ON_DELIVERY_PRICE;
                break;
        }
        if (!$withVat) {
            return $priceWithVat / 1.21;
        } else {
            return $priceWithVat;
        }
    }


    public function getAddressString(): string
    {
        return $this->street . ' ' . $this->deliveryStreet . ', ' . $this->deliveryCity . ' ' . $this->deliveryPsc;
    }

    public function getShippingOrderItem(): ?OrdersItem
    {
        return $this->ordersItems->toCollection()->getBy(['type' => OrdersItem::TYPE_SHIPPING]);
    }

    public function getPaymentOrderItem(): ?OrdersItem
    {
        return $this->ordersItems->toCollection()->getBy(['type' => OrdersItem::TYPE_PAYMENT]);
    }
}