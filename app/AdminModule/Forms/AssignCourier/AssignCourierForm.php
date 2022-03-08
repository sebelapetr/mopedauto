<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Order;
use App\Model\OrderLog;
use App\Model\Orm;
use App\Model\Role;
use Contributte\Translation\Translator;
use Nette;

class AssignCourierForm extends Nette\Application\UI\Control
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
        $this->template->setFile(__DIR__ . '/AssignCourierForm.latte');
        $this->template->order = $this->order;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addSelect('courier', 'Kurýr', $this->orm->users->findBy(['role->intName' => Role::INT_NAME_COURIER, 'workingNow' => true])->fetchPairs('id', 'surname'))
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSubmit('send', 'Přiřadit kurýra')
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