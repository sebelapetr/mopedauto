<?php declare(strict_types=1);

namespace App\Model\Services;

use Nette\Utils\ArrayHash;
use Nette\Utils\Random;
use App\Model\Customer;
use App\Model\Emails\CustomerEmailService;
use App\Model\Orm;
use App\Model\Role;
use App\Model\User;
use Tracy\Debugger;
use Tracy\ILogger;

class CustomerRegistrationService
{
    public Orm $orm;

    public CustomerEmailService $customerEmailService;

    public function __construct(Orm $orm, CustomerEmailService $customerEmailService)
    {
        $this->orm = $orm;
        $this->customerEmailService = $customerEmailService;
    }

    public function createNewCustomer(ArrayHash $data): Customer
    {
        try {
            $customer = $this->createCustomer($data);
        } catch (\Exception $exception) {
            Debugger::log($exception->getMessage(), ILogger::ERROR);
            throw new \Exception('Chyba ve vytváření uživatele.');
        }

        return $customer;
    }

    private function createCustomer(ArrayHash $data): Customer
    {
        $customer = new Customer();
        $customer->name = $data->name;
        $customer->surname = $data->surname;
        $customer->email = $data->email;
        $customer->phoneNumber = $data->phoneNumber;
        $customer->active = false;
        $customer->confirmToken = Random::generate(20);
        $customer->confirmed = false;
        $customer->createdAt = new \DateTimeImmutable();
        $customer->newsletterAgreementAt = $data->newsletterAgreementAt == true ? new \DateTimeImmutable() : null;
        $customer->gdprAgreementAt = $data->gdprAgreementAt == true ? new \DateTimeImmutable() : null;

        $customer->password = $customer->passwords->hash($data->password);

        $this->orm->persistAndFlush($customer);
        return $customer;
    }
}
