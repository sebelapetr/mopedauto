<?php

namespace App\Model;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;

/**
 * Class OrdersRepository
 * @package App\Model
 *
 */

class OrdersRepository extends Repository{

    /**
     * Returns possible entity class names for current repository.
     * @return string[]
     */
    public static function getEntityClassNames(): array
    {
        return [Order::class];
    }

    public function findAllFinished(array $args): ICollection
    {
        return $this->findBy(['state!=' => Order::ORDER_STATE_UNFINISHED] + $args);
    }

    public function getDayTurnover(\DateTimeImmutable $date): float
    {
        $turnover = 0;
        $orders = $this->findBy(['createdAt>=' => $date->setTime(0,0,0), 'createdAt<=' => $date->setTime(23,59,59), "state!=" => Order::ORDER_STATE_UNFINISHED]);
        /** @var Order $order */
        foreach ($orders as $order)
        {
            $turnover += $order->totalPriceVat;
        }
        return $turnover;
    }

    public function getDayOrdersSold(\DateTimeImmutable $date): int
    {
        return $this->findBy(['createdAt>=' => $date->setTime(0,0,0), 'createdAt<=' => $date->setTime(23,59,59), "state!=" => Order::ORDER_STATE_UNFINISHED])->countStored();
    }

    public function getDayProductsSold(\DateTimeImmutable $date): int
    {
        $quantity = 0;
        $orders = $this->findBy(['createdAt>=' => $date->setTime(0,0,0), 'createdAt<=' => $date->setTime(23,59,59)]);
        /** @var Order $order */
        foreach ($orders as $order)
        {
            foreach ($order->ordersItems as $orderItem)
            {
                $quantity += $orderItem->quantity;
            }
        }
        return $quantity;
    }

}