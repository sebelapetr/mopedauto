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

    public function reset()
    {
        $this->sessionSection->products = null;
    }

}
