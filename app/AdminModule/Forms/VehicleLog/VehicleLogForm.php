<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Orm;
use App\Model\User;
use App\Model\VehicleLog;
use Contributte\Translation\Translator;
use Model\Enum\VehicleBrandsEnum;
use Model\Enum\VehicleLogEnum;
use Nette;
use Tracy\Debugger;

class VehicleLogForm extends Nette\Application\UI\Control
{
    private Orm $orm;
    public ?VehicleLog $vehicleLog;
    public Translator $translator;

    public function __construct(Orm $orm, Translator $translator, ?VehicleLog $vehicleLog)
    {
        $this->orm = $orm;
        $this->vehicleLog = $vehicleLog;
        $this->translator = $translator;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/vehicleLogForm.latte');
        $this->template->vehicleLog = $this->vehicleLog;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        foreach ( VehicleLogEnum::getEnum() as $id => $event)
        {
            $events[$id] = $this->translator->translate('entity.vehicleLog.event'.$event);
        }
        $form->addSelect('event', 'Událost', $events)
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSelect('vehicle', 'Vozidlo', $this->orm->vehicles->findAll()->fetchPairs('id','spz'))
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSubmit('send', $this->vehicleLog ? 'Upravit' : 'Přidat')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');


        $form->onSuccess[] = [$this, 'onSuccess'];

        if ($this->vehicleLog)
        {
            $defaults=$this->vehicleLog->toArray();
            unset($defaults['vehicle']);
            $defaults['vehicle'] = $this->vehicleLog->vehicle->id;
            $form->setDefaults($defaults);
        }

        return $form;
    }


    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        if (!$this->vehicleLog)
        {
            $vehicleLog = new VehicleLog();
        } else {
            $vehicleLog = $this->vehicleLog;
        }

        $vehicleLog->event = $values->event;
        $vehicleLog->vehicle = $this->orm->vehicles->getById($values->vehicle);
        $vehicleLog->createdBy = $this->getPresenter()->user->user;

        $this->orm->persistAndFlush($vehicleLog);

        if ($this->vehicleLog) {
            $this->getPresenter()->flashMessage('Záznam vozidla byl upraven');
        } else {
            $this->getPresenter()->flashMessage('Záznam vozidla byl přidán');
        }

        $this->getPresenter()->redirect('edit', ['id' => $vehicleLog->id]);
    }
}