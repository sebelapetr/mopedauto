<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\OrderLog;
use App\Model\Orm;
use App\Model\Role;
use App\Model\User;
use App\Model\VehicleLog;
use Contributte\Translation\Translator;
use Model\Enum\VehicleBrandsEnum;
use Model\Enum\VehicleLogEnum;
use Nette;
use Tracy\Debugger;

class OrderLogForm extends Nette\Application\UI\Control
{
    private Orm $orm;
    public ?OrderLog $orderLog;
    public Translator $translator;

    public function __construct(Orm $orm, Translator $translator, ?OrderLog $orderLog)
    {
        $this->orm = $orm;
        $this->orderLog = $orderLog;
        $this->translator = $translator;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/orderLogForm.latte');
        $this->template->orderLog = $this->orderLog;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();


        foreach ( $this->orm->orderStates->findAll()->fetchPairs('id', 'type') as $id => $type)
        {
            $types[$id] = $this->translator->translate('entity.orderLog.orderState'.$type);
        }


        $select = $this->orm->users->findBy(['role->intName' => [Role::INT_NAME_COURIER, Role::INT_NAME_ADMIN, Role::INT_NAME_OPERATOR]])->fetchPairs('id', 'surname');
        $select = array_merge([NULL=>''],$select);

        $form->addSelect('order', 'Objednávka', $this->orm->orders->findAll()->fetchPairs('id', 'id'))
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSelect('orderState','Stav', $types)
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSelect('assignedTo', 'Přiřazeno', $select)
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('send', $this->orderLog ? 'Upravit' : 'Přidat')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');


        $form->onSuccess[] = [$this, 'onSuccess'];

        return $form;
    }


    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        if (!$this->orderLog)
        {
            $orderLog = new OrderLog();
        } else {
            $orderLog = $this->orderLog;
        }

        $orderLog->order = $this->orm->orders->getById($values->order);
        $orderLog->orderState = $this->orm->orderStates->getById($values->orderState);
        $orderLog->assignedTo = $this->orm->users->getById($values->assignedTo);
        $orderLog->createdBy = $this->getPresenter()->user->user;

        $this->orm->persistAndFlush($orderLog);


            $this->getPresenter()->flashMessage('Záznam objednávky byl přidán');

    }
}