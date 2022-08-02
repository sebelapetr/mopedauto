<?php

namespace App\Model\Session;

use App\Model\Order;
use App\Model\OrdersItem;
use App\Model\Orm;
use App\Model\Poster;
use App\Model\Product;
use Nette\Http\SessionSection;
use Nette\Utils\Random;
use Tracy\Debugger;

class CartService
{
    private SessionSection $sessionSection;
    private Orm $orm;


    public function __construct(SessionSection $sessionSection, Orm $orm)
    {
        $this->sessionSection = $sessionSection;
        $this->orm = $orm;
        $this->initOrderSection();
    }

    private function initOrderSection(): void
    {
        if (!$this->sessionSection->order || $this->sessionSection->order === null) {
            $this->setOrderSection();
        } else {
            $this->getOrder();
        }
    }

    private function setOrderSection(): void
    {
        $hash = Random::generate(20);

        if (!$this->orm->orders->getBy(['hash' => $hash])) {
            $order = $this->createNewOrder($hash);
            $this->sessionSection->order = [
                'hash' => $order->hash
            ];
        } else {
            $this->setOrderSection();
        }
    }


    private function createNewOrder(string $hash): Order
    {
        $order = new Order();
        $order->hash = $hash;
        $this->orm->persistAndFlush($order);
        return $order;
    }

    public function getOrder(): Order
    {
        $hash = $this->sessionSection->order['hash'];
        $order = $this->orm->orders->getBy(['hash' => $hash]);

        if ($order && $order->state === Order::ORDER_STATE_UNFINISHED) {
            return $order;
        } else {
            $this->setOrderSection();
            return $this->getOrder();
        }
    }

    public function addProductToCart(Product $product, int $quantity): bool
    {
        $order = $this->getOrder();
        $existingOrderItem = $order->ordersItems->toCollection()->getBy(['product' => $product]);
        if ($existingOrderItem) {
            $orderItems = $existingOrderItem;
            $orderItems->quantity++;
        } else {
            $orderItems = new OrdersItem();
            $orderItems->name = $product->productName;
            $orderItems->pricePieceVat = $product->catalogPriceVat;
            $orderItems->pricePiece = round($product->catalogPriceVat / 1.21, 2);
            $orderItems->priceVat = $orderItems->pricePieceVat * $quantity;
            $orderItems->price = $orderItems->pricePiece * $quantity;
            $orderItems->quantity = $quantity;
            $orderItems->vat = 21;
            $orderItems->type = OrdersItem::TYPE_PRODUCT;
            $orderItems->product = $product;
            $orderItems->order = $order;
        }
        try {
            $this->orm->persistAndFlush($orderItems);
        } catch (\Exception $e) {
            Debugger::log($e);
        }
        $this->recountOrder();
        return true;
    }

    public function removeProductFromCart(OrdersItem $orderItem)
    {
        $order = $this->getOrder();
        $this->orm->remove($order->ordersItems->toCollection()->getById($orderItem->id));
        $this->orm->flush();
        if ($order->ordersItems->toCollection()->findBy(["product!=" => null])->countStored() === 0) {
            foreach ($order->ordersItems as $orderItem) {
                $this->orm->removeAndFlush($orderItem);
            }
        }
        $this->recountOrder();
    }


    public function addProductQuantity(OrdersItem $orderItem)
    {
        $product = $orderItem->product;
        $orderItem->quantity++;
        if ($product->catalogPriceVat) {
            $orderItem->pricePieceVat = $product->catalogPriceVat;
            $orderItem->pricePiece = round($product->catalogPriceVat / 1.21, 2);
            $orderItem->priceVat = $orderItem->pricePieceVat * $orderItem->quantity;
            $orderItem->price = $orderItem->pricePiece * $orderItem->quantity;
        }
        $this->orm->persistAndFlush($orderItem);
        $this->recountOrder();
    }

    public function removeProductQuantity(OrdersItem $orderItem)
    {
        $product = $orderItem->product;
        $orderItem->quantity--;
        if ($product->catalogPriceVat) {
            $orderItem->pricePieceVat = $product->catalogPriceVat;
            $orderItem->pricePiece = round($product->catalogPriceVat / 1.21, 2);
            $orderItem->priceVat = $orderItem->pricePieceVat * $orderItem->quantity;
            $orderItem->price = $orderItem->pricePiece * $orderItem->quantity;
        }
        if ($orderItem->quantity <= 0) {
            $this->removeProductFromCart($orderItem);
        }
        $this->orm->persistAndFlush($orderItem);
        $this->recountOrder();
    }

    public function reset()
    {
        unset($this->sessionSection->order);
        $this->initOrderSection();
    }

    public function recountOrder()
    {
        $priceWithoutTax = 0;
        $priceWithTax = 0;
        $order = $this->getOrder();
        foreach ($order->ordersItems as $orderItem)
        {
            $priceWithoutTax += $orderItem->price;
            $priceWithTax += $orderItem->priceVat;
        }
        $order->totalPrice = $priceWithoutTax;
        $order->totalPriceVat = $priceWithTax;
        $this->orm->persistAndFlush($order);
    }

    public function hasHeavyProduct(): bool
    {
        foreach($this->getOrder()->ordersItems as $orderItem) {
            if ($orderItem->product && $orderItem->product->isHeavy) {
                return true;
            }
        }
        return false;
    }

}
