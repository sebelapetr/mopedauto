<?php

namespace App\Model;

use App\Model\Orm;
use App\Model\Session\CartSession;

class AddProductService
{
    private Orm $orm;

    public CartSession $cartSession;

    public function __construct(Orm $orm, CartSession $cartSession)
    {
        $this->orm = $orm;
        $this->cartSession = $cartSession;
    }

    public function addProduct($values)
    {
        $product = $this->orm->products->getById($values->id);

        $data["id"] = $product->id;
        $data["productName"] = $product->productName;
        $data["catalogPriceVat"] = $product->catalogPriceVat;
        $data["quantity"] = $values->quantity;
        $data["photo"] = $product->image;
        $this->cartSession->addProduct($data);
    }
}