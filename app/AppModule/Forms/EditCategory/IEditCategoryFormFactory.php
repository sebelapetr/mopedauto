<?php
declare(strict_types=1);

namespace App\AppModule\Forms;


interface IEditCategoryFormFactory{
    /** @return EditCategoryForm */
    function create($id);
}