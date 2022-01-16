<?php
declare(strict_types=1);

namespace App\AppModule\Forms;


interface IAddProductFormFactory{
    /** @return AddProductForm */
    function create();
}