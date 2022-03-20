<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Order;
use App\Model\OrderLog;
use App\Model\OrderState;
use App\Model\Orm;
use App\Model\Role;
use App\Model\User;
use App\Model\VehicleLog;
use Contributte\Translation\Translator;
use Model\Enum\VehicleBrandsEnum;
use Model\Enum\VehicleLogEnum;
use Nette;
use Tracy\Debugger;

class OrderForm extends Nette\Application\UI\Control
{
    private Orm $orm;
    public Translator $translator;

    public function __construct(Orm $orm, Translator $translator)
    {
        $this->orm = $orm;
        $this->translator = $translator;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/OrderForm.latte');
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addText('eanCode', 'EAN kód')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('contactName', 'Kontaktní Jméno')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('contactSurname', 'Kontaktní Příjmení')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('addressNumber', 'Číslo popisné pro doručení')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('street', 'Ulice doručení')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $zips = [];
        foreach ($this->orm->zips->findAll() as $zip)
        {
            $zips[$zip->id] = $zip->city->name . ' - ' . $zip->zip;
        }

        asort($zips);

        $form->addSelect('zip', 'ZIP', $zips)
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('phoneNumber', 'Kontaktní telefon')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('email', 'Kontaktní email')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('nameInvoicing', 'Fakturace - Jméno')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('surnameInvoicing', 'Fakturace - Příjmení')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('ic', 'Fakturace - IČ')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('dic', 'Fakturace - DIČ')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('streetInvoicing', 'Fakturace - Ulice')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSelect('invoicingZip', 'Fakturace - ZIP', $zips)
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('deliveryDate', 'Datum doručení')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlType('datetime-local')
            ->setRequired();

        $form->addSelect('partnerBranch', 'Partnerská pobočka', $this->orm->partnersBranches->findAll()->fetchPairs('id', 'name'))
            ->setHtmlAttribute('class', 'form-control');

        $paymentTypes = [];
        foreach ( $this->orm->paymentTypes->findAll()->fetchPairs('id', 'type') as $id => $type)
        {
            $paymentTypes[$id] = $this->translator->translate('entity.paymentType.type'.$type);
        }
        $form->addSelect('paymentType', 'Typ platby', $paymentTypes)
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $deliveryTypes = [];
        foreach ( $this->orm->deliveryTypes->findAll()->fetchPairs('id', 'type') as $id => $type)
        {
            $deliveryTypes[$id] = $this->translator->translate('entity.deliveryType.type'.$type);
        }
        $form->addSelect('deliveryType', 'Typ doručení', $deliveryTypes)
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('orderSource', 'Zdroj objednávky')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('send', 'Vytvořit objednávku')
            ->setHtmlAttribute('class', 'btn btn-success btn-md');


        $form->onSuccess[] = [$this, 'onSuccess'];

        return $form;
    }


    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        $order = new Order();
        $order->createdAt = new \DateTimeImmutable();
        $order->onAttach($this->orm->orders, $order->getMetadata());

        foreach ($values as $column => $value) {
            $order->__set($column, $value);
        }

        $order->city = $order->zip->city;
        $order->invoicingCity = $order->invoicingZip->city;

        $order->termsOfServicesAgreement = true;

        $this->orm->persistAndFlush($order);

        $orderLog = new OrderLog();
        $orderLog->orderState = $this->orm->orderStates->getBy(['type' => 'CREATED']);
        $orderLog->order = $order;
        $orderLog->createdBy = $this->getPresenter()->getUser()->user;
        $orderLog->assignedTo = null;
        $orderLog->createdAt = new \DateTimeImmutable();
        $this->orm->persistAndFlush($orderLog);

        $this->getPresenter()->flashMessage('Objednávka byla přidaná.');
        $this->getPresenter()->redirect('addItems', ['orderId' => $order->id]);

    }
}