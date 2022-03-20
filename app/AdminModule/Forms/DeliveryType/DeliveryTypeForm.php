<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\DeliveryType;
use App\Model\Orm;
use Model\Enum\DeliveryTypeEnum;
use Nette;
use Tracy\Debugger;

class DeliveryTypeForm extends Nette\Application\UI\Control
{
    private Orm $orm;
    public ?DeliveryType $deliveryType;

    public function __construct(Orm $orm, ?DeliveryType $deliveryType)
    {
        $this->orm = $orm;
        $this->deliveryType = $deliveryType;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/DeliveryTypeForm.latte');
        $this->template->deliveryType = $this->deliveryType;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addSelect('type', 'Typ', DeliveryTypeEnum::getEnum())
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addTextArea('description', 'Popis')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSubmit('send', $this->deliveryType ? 'Upravit' : 'Přidat')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onValidate[] = [$this, 'onValidate'];
        $form->onSuccess[] = [$this, 'onSuccess'];

        if ($this->deliveryType)
        {
            $form->setDefaults($this->deliveryType->toArray());
        }

        return $form;
    }

    public function onValidate(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();
        if ($this->orm->deliveryTypes->getBy(['type' => $values->type]) && !$this->deliveryType)
        {
            $form->addError('Typ doručení se stejným typem již existuje.');
            return;
        }
    }

    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        if (!$this->deliveryType)
        {
            $deliveryType = new DeliveryType();
        } else {
            $deliveryType = $this->deliveryType;
        }

        $deliveryType->description = $values->description;
        $deliveryType->type = $values->type;


        $this->orm->persistAndFlush($deliveryType);

        if ($this->deliveryType) {
            $this->getPresenter()->flashMessage('Typ doručení byl upraven');
        } else {
            $this->getPresenter()->flashMessage('Typ doručení byl přidán');
        }

        $this->getPresenter()->redirect('edit', ['id' => $deliveryType->id]);
    }
}