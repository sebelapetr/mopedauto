<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\IShippingAndPaymentFormFactory;
use App\FrontModule\Forms\IPersonalDataFormFactory;
use App\Model\Session\CartSession;
use Nette\Utils\DateTime;
use Tracy\Debugger;

class CartPresenter extends BasePresenter
{

    /** @inject */
    public IShippingAndPaymentFormFactory $shippingAndPaymentFormFactory;

    /** @inject */
    public IPersonalDataFormFactory $personalDataFormFactory;

    public function renderStep1()
    {
        $this->getTemplate()->cartCheck = 1;
        $this->getTemplate()->productsInCart = $this->getProductsInCart();
        $this->getTemplate()->totalPrice = $this->getTotalPrice();
    }

    public function renderStep2()
    {
        $this->getTemplate()->cartCheck = 1;
        $this->getTemplate()->productsInCart = $this->getProductsInCart();
        $this->getTemplate()->totalPrice = $this->getTotalPrice();
    }

    public function renderStep3($id)
    {
        $id = str_replace('8452', '', $id);
        $order = $this->orm->orders->getById(base64_decode($id));
        $deliveryDate = DateTime::from($order->createdAt);
        if ($deliveryDate->format("N") == 5) {
            $days = 6;
        } elseif ($deliveryDate->format("N") == 6) {
            $days = 5;
        } elseif ($deliveryDate->format("N") == 7) {
            $days = 4;
        }
        else {
            $days = 4;
        }
        $deliveryDate->modify('+'.$days.' days');
        $this->getTemplate()->order = $order;
        $this->getTemplate()->deliveryDate = $deliveryDate;
    }

    public function getProductsInCart(){
        return $this->cartSession->getProducts();
    }

    public function getTotalPrice(){
        $products = $this->cartSession->getProducts();
        $shippingSession = $this->getSession()->getSection('shipping');
        $paymentSession = $this->getSession()->getSection('payment');
        $totalPrice = 0;
        if ($products !== null && count($products) > 0) {
            foreach ($products as $product) {
                $totalPrice += $product['catalogPriceVat'] * $product['quantity'];
            }
            $shipping = ($shippingSession->shipping == 1 ? '98' : '0');
            $payment = ($paymentSession->payment == 1 ? '30' : '0');
            $totalPrice += $shipping;
            $totalPrice += $payment;
        }
        return $totalPrice;
    }

    public function createComponentShippingAndPaymentForm()
    {
        return $this->shippingAndPaymentFormFactory->create();
    }

    public function createComponentPersonalDataForm()
    {
        return $this->personalDataFormFactory->create();
    }

    public function handleRemoveProduct($id)
    {

    }

    public function handleAddProductNumber($id)
    {

    }

    public function handleRemoveProductNumber($id)
    {

    }
}