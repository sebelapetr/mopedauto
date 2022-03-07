<?php

namespace App\Model;

use App\Model\Orm;
use App\Model\Session\CartService;

class AddProductService
{
    private Orm $orm;

    public CartService $cartService;

    public function __construct(Orm $orm, CartService $cartService)
    {
        $this->orm = $orm;
        $this->cartService = $cartService;
    }

    public function addProduct($values)
    {
        $product = $this->orm->products->getById($values->id);
        $this->cartService->addProductToCart($product, $values->quantity);
    }
}