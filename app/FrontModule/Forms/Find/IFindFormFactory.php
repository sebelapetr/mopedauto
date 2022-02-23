<?php
declare(strict_types=1);

namespace App\FrontModule\Forms;

interface IFindFormFactory
{
    function create(): FindForm;
}