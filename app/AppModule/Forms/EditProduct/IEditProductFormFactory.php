<?php
declare(strict_types=1);

namespace App\AppModule\Forms;

interface IEditProductFormFactory
{
    function create($id): EditProductForm;
}