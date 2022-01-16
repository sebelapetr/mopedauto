<?php

namespace App\Model;

use Nette\Security\Passwords;
use Tracy\Debugger;

class AddProductService
{

    /** @var Orm */
    private $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function addProduct($values, $productsSession)
    {
        $product = $this->orm->products->getById($values->id);
        $id = $values->id;
        //$array = ['id'=>$id, 'productName'=>$product->productName,'catalogPriceVat'=>$product->catalogPriceVat,'quantity'=>1, 'photo'=>$product->image];
        $productsSession->$id = array();
        $productsSession->$id['id'] = $id;
        $productsSession->$id['productName'] = $product->productName;
        $productsSession->$id['catalogPriceVat'] = $product->catalogPriceVat;
        $productsSession->$id['quantity'] = $values->quantity;
        $productsSession->$id['photo'] = $product->image;
    }
}