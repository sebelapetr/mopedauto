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
        $orderItems = new OrdersItem();
        $orderItems->name = $product->productName;
        $orderItems->priceVat = $product->catalogPriceVat * $quantity;
        $orderItems->price = $orderItems->priceVat / 1.21;
        $orderItems->quantity = $quantity;
        $orderItems->vat = 21;
        $orderItems->type = OrdersItem::TYPE_PRODUCT;
        $orderItems->product = $product;
        $orderItems->order = $order;
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
        $this->recountOrder();
    }


    public function addProductQuantity(OrdersItem $orderItem)
    {
        $product = $orderItem->product;
        $orderItem->quantity++;
        $orderItem->price = $product->catalogPrice ? $product->catalogPrice * $orderItem->quantity : 0;
        $orderItem->priceVat = $product->catalogPriceVat * $orderItem->quantity;
        $this->orm->persistAndFlush($orderItem);
        $this->recountOrder();
    }

    public function removeProductQuantity(OrdersItem $orderItem)
    {
        $product = $orderItem->product;
        $orderItem->quantity--;
        $orderItem->price = $product->catalogPrice ? $product->catalogPrice * $orderItem->quantity : 0;
        $orderItem->priceVat = $product->catalogPriceVat * $orderItem->quantity;
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

}
