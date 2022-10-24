<?php
declare(strict_types=1);

namespace App\Model\Emails;

use Nette\Utils\ArrayHash;
use App\Model\Customer;
use Tracy\Debugger;

class CustomerEmailService extends EmailService
{
    public function getEmailNamespace(): string {
        return "Customer";
    }

    public function confirmRegistration(ArrayHash $data, Customer $customer): void
    {
        $registrationLink = $this->linkGenerator->link('Front:Registration:confirmEmail', ['hash' => $customer->confirmToken]);
        $name = $customer->name . ' ' . $customer->surname;
        $this->createAndSendMessage("confirmRegistration", $data->email, ['data' => $data, 'confirmLink' => $registrationLink, 'name' => $name], 'Potvrďte registraci', false);
    }
}