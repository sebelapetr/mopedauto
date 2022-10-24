<?php declare(strict_types=1);

namespace App\FrontModule\Forms;

interface ILoginEmailFormFactory
{
    public function create(): LoginEmailForm;
}
