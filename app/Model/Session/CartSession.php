<?php

namespace App\Model\Session;

use Nette\Http\SessionSection;

class CartSession
{
    /** @var SessionSection */
    private $sessionSection;


    public function __construct(SessionSection $sessionSection)
    {
        $this->sessionSection = $sessionSection;
    }


    public function getProducts()
    {
        return $this->sessionSection->products;
    }


    public function addProduct($data)
    {
        $this->sessionSection->products[$data["id"]] = $data;
    }

    public function removeProduct($id)
    {
        unset($this->sessionSection->products[$id]);
    }

    public function addProductQuantity($id)
    {
        $this->sessionSection->products[$id]['quantity']++;
    }

    public function removeProductQuantity($id)
    {
        $quantity = $this->sessionSection->products[$id]['quantity'];
        $quantity--;
        if ($quantity <= 0) {
            $this->removeProduct($id);
        }
    }

    public function reset()
    {
        $this->sessionSection->products = null;
    }

}
