<?php

namespace App\FrontModule\Forms;

use App\Model\Order;
use App\Model\OrdersItem;
use App\Model\Orm;
use App\Model\QuoteService;
use App\Model\Session\CartService;
use Contributte\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

interface IShippingAndPaymentFormFactory{
    /** @return ShippingAndPaymentForm */
    function create();
}

class ShippingAndPaymentForm extends Control
{

    public CartService $cartService;

    public Orm $orm;

    public Translator $translator;

    public function __construct(CartService $cartService, Orm $orm, Translator $translator)
    {
        $this->cartService = $cartService;
        $this->orm = $orm;
        $this->translator = $translator;
    }

    protected function createComponentShippingAndPaymentForm()
    {
        $form = new Form();

        $deliveries = [];
        $deliveries[Order::TYPE_DELIVERY_PERSONAL] = 'Osobní odběr';

        if ($this->cartService->hasHeavyProduct()) {
            $deliveries[Order::TYPE_DELIVERY_ADDRESS_BIG] = 'TOPTRANS velký balík';
        } else {
            $deliveries[Order::TYPE_DELIVERY_ADDRESS] = 'TOPTRANS malý balík';
        }

        $form->addRadioList('shipping', 'Způsob doručení', $deliveries)
            ->setDefaultValue($this->cartService->getOrder()->typeDelivery)
            ->setRequired('Vyberte typ dopravy')
            ->addCondition($form::FILLED, true)
            ->toggle('payment-container')
            ->addCondition($form::EQUAL, Order::TYPE_DELIVERY_PERSONAL)
            ->toggle('payment-type-'.Order::TYPE_PAYMENT_CASH)
            ->toggle('payment-type-'.Order::TYPE_PAYMENT_CARD)
            ->elseCondition()
            ->toggle('payment-type-'.Order::TYPE_PAYMENT_CASH_ON_DELIVERY)
            ->toggle('payment-type-'.Order::TYPE_PAYMENT_CARD);

        $form->addRadioList('payment', 'Způsob platby', [
            Order::TYPE_PAYMENT_CASH => 'V hotovosti',
            Order::TYPE_PAYMENT_CASH_ON_DELIVERY => 'Dobírka',
            Order::TYPE_PAYMENT_CARD => 'Kartou'
        ])

            ->setDefaultValue($this->cartService->getOrder()->typePayment)
            ->setRequired('Vyberte typ platby');

        $form->addSubmit('submit', 'Pokračovat v objednávce')
            ->setHtmlAttribute("id", "submitShippingAndPaymentForm");
        $form->onSuccess[] = [$this, 'shippingAndPaymentFormSucceeded'];

        return $form;
    }

    public function shippingAndPaymentFormSucceeded(Form $form, ArrayHash $values)
    {
        $order = $this->cartService->getOrder();

        $items = $order->ordersItems->toCollection();
        $shipping = $items->getBy(["type" => OrdersItem::TYPE_SHIPPING]);
        if ($shipping) {
            $this->orm->remove($shipping);
        }
        $payment = $items->getBy(["type" => OrdersItem::TYPE_PAYMENT]);
        if ($payment) {
            $this->orm->remove($payment);
        }
        $this->orm->flush();

        $order->typeDelivery = $values->shipping;
        $orderItemsShipping = new OrdersItem();
        $orderItemsShipping->name = $this->translator->translate('entity.order.shipping_'.$values->shipping);
        $orderItemsShipping->type = OrdersItem::TYPE_SHIPPING;
        $orderItemsShipping->price = $order->getShippingPrice(false);
        $orderItemsShipping->priceVat = $order->getShippingPrice(true);
        $orderItemsShipping->quantity = 1;
        $orderItemsShipping->vat = 21;
        $orderItemsShipping->order = $order;
        $this->orm->persistAndFlush($orderItemsShipping);
        $this->cartService->recountOrder();

        $order->typePayment = $values->payment;
        $orderItemsPayment = new OrdersItem();
        $orderItemsPayment->name = $this->translator->translate('entity.order.payment_'.$values->payment);
        $orderItemsPayment->type = OrdersItem::TYPE_PAYMENT;
        $orderItemsPayment->price = $order->getPaymentPrice(false);
        $orderItemsPayment->priceVat = $order->getPaymentPrice(false);
        $orderItemsPayment->quantity = 1;
        $orderItemsPayment->vat = 21;
        $orderItemsPayment->order = $order;
        $this->orm->persistAndFlush($orderItemsPayment);
        $this->cartService->recountOrder();

        $this->getPresenter()->redirect('step2');
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/ShippingAndPayment/ShippingAndPayment.latte");
        $this->getTemplate()->hasHeavyProduct = $this->cartService->hasHeavyProduct();
        $this->getTemplate()->render();
    }

}