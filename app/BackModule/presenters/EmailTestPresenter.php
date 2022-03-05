<?php

namespace App\BackModule\Presenters;

class EmailTestPresenter extends BasePresenter {

    public function renderOrderSent(){
        $this->getTemplate()->setFile(__DIR__.'/../templates/Emails/orderSentTest.latte');
        $order = $this->orm->orders->getById(54);

        $this->template->order = [
            'id' => $order->id,
            'name' => $order->name,
            'ordersItems' => $order->ordersItems,
            'totalPriceVat' => $order->totalPriceVat,
        ];
    }

}