<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Orm;
use App\Model\PaymentType;
use Contributte\Translation\Translator;
use Model\Enum\PaymentTypeEnum;
use Nette;
use Tracy\Debugger;

class PaymentTypeForm extends Nette\Application\UI\Control
{
    private Orm $orm;
    public ?PaymentType $paymentType;
    public Translator $translator;

    public function __construct(Orm $orm, Translator $translator, ?PaymentType $paymentType)
    {
        $this->orm = $orm;
        $this->paymentType = $paymentType;
        $this->translator = $translator;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/PaymentTypeForm.latte');
        $this->template->paymentType = $this->paymentType;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        foreach ( PaymentTypeEnum::getEnum() as $id => $type)
        {
            $types[$id] = $this->translator->translate('entity.paymentType.type'.$type);
        }
        $form->addSelect('type', 'Typ', $types)
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addTextArea('description', 'Popis')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSelect('active', 'Aktivní?', [1 => 'Ano', 0 => 'Ne'])
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('send', $this->paymentType ? 'Upravit' : 'Přidat')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onValidate[] = [$this, 'onValidate'];
        $form->onSuccess[] = [$this, 'onSuccess'];

        if ($this->paymentType)
        {
            $defaults = $this->paymentType->toArray();
            $active = $defaults['active'];
            unset($defaults['active']);
            $defaults['active'] = $active ? 1 : 0;
            $form->setDefaults($defaults);
        }

        return $form;
    }

    public function onValidate(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();
        if ($this->orm->paymentTypes->getBy(['type' => $values->type]) && !$this->paymentType)
        {
            $form->addError('Typ způsobu platby se stejným typem již existuje.');
            return;
        }
    }

    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        if (!$this->paymentType)
        {
            $paymentType = new PaymentType();
        } else {
            $paymentType = $this->paymentType;
        }

        $paymentType->description = $values->description;
        $paymentType->type = $values->type;
        $paymentType->active = $values->active;

        $this->orm->persistAndFlush($paymentType);

        if ($this->paymentType) {
            $this->getPresenter()->flashMessage('Typ platby byl upraven');
        } else {
            $this->getPresenter()->flashMessage('Typ platby byl přidán');
        }

        $this->getPresenter()->redirect('edit', ['id' => $paymentType->id]);
    }
}