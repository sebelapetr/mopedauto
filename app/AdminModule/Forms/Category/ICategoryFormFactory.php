<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Category;

interface ICategoryFormFactory
{
    function create(?Category $category): CategoryForm;
}