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

    public function addProduct($id, $quantity)
    {
        $product = $this->orm->products->getById($id);
        $this->cartService->addProductToCart($product, $quantity);
    }
}