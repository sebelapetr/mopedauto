<?php
declare(strict_types=1);

namespace App\AppModule\Forms;


interface IEditParentCategoryFormFactory
{
    function create(): ZipForm;
}