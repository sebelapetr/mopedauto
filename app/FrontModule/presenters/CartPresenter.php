<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\IShippingAndPaymentFormFactory;
use App\Model\Session\CartSession;
use Tracy\Debugger;

class CartPresenter extends BasePresenter
{
    /** @inject */
    public CartSession $cartSession;

    /** @inject */
    public IShippingAndPaymentFormFactory $shippingAndPaymentFormFactory;

    public function renderStep1()
    {
        $this->getTemplate()->cartCheck = 1;
        $this->getTemplate()->productsInCart = $this->getProductsInCart();
        $this->getTemplate()->totalPrice = $this->getTotalPrice();
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