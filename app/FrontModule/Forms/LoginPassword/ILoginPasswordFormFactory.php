<?php declare(strict_types=1);

namespace App\FrontModule\Forms;

interface ILoginPasswordFormFactory
{
    public function create(string $userEmail): LoginPasswordForm;
}
