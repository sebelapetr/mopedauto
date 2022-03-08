<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Order;
use App\Model\OrderLog;
use App\Model\Orm;
use App\Model\Role;
use Contributte\Translation\Translator;
use Nette;
use Tracy\Debugger;

class OrderStateForm extends Nette\Application\UI\Control
{
    private Orm $orm;
    public Order $order;
    public Translator $translator;

    public function __construct(Orm $orm, Translator $translator, Order $order)
    {
        $this->orm = $orm;
        $this->order = $order;
        $this->translator = $translator;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/OrderStateForm.latte');
        $this->template->order = $this->order;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $states = [];
        foreach ($this->orm->orderStates->findAll() as $orderState)
        {
            $states[$orderState->id] = $this->translator->translate('entity.orderLog.orderState'.$orderState->type);
        }
        $form->addSelect('state', 'Stav:', $states)
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $users = [null => ''];
        foreach ($this->orm->users->findBy(['role->intName' => Role::INT_NAME_COURIER, 'workingNow' => true]) as $user)
        {
            $users[$user->id] = $user->name . ' ' . $user->surname;
        }
        $form->addSelect('assignTo', 'Přiřadit uživateli:', $users)
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('send', 'Změnit stav objednávky')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');


        $form->onSuccess[] = [$this, 'onSuccess'];

        return $form;
    }


    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        $orderLog = new OrderLog();

        $orderLog->order = $this->order;
        $orderLog->orderState = $this->orm->orderStates->getById($values->state);
        if ($values->assignTo)
        {
            $orderLog->assignedTo = $this->orm->users->getById($values->assignTo);
        }
        $orderLog->createdBy = $this->getPresenter()->user->user;

        $this->orm->persistAndFlush($orderLog);

        $this->getPresenter()->flashMessage('Stav objednávky byl změněný');
        $this->getPresenter()->redirect('this');

    }
}