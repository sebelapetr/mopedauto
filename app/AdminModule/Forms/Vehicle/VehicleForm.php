<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Orm;
use App\Model\Vehicle;
use Model\Enum\ColorsEnum;
use Model\Enum\VehicleBrandsEnum;
use Nette;
use Tracy\Debugger;

class VehicleForm extends Nette\Application\UI\Control
{
    private Orm $orm;
    public ?Vehicle $vehicle;

    public function __construct(Orm $orm, ?Vehicle $vehicle)
    {
        $this->orm = $orm;
        $this->vehicle = $vehicle;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/VehicleForm.latte');
        $this->template->vehicle = $this->vehicle;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addText('spz', 'SPZ')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSelect('vehicleBrand', 'Značka', VehicleBrandsEnum::getEnum())
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('vehicleModel', 'Model')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSelect('vehicleColor', 'Barva', ColorsEnum::getEnum())
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('startKm', 'Km na začátku')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('endKm', 'Km na konci')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSelect('type', 'Typ', [Vehicle::TYPE_CAR => 'Auto', Vehicle::TYPE_SCOOTER => 'Skůtr'])
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSubmit('send', $this->vehicle ? 'Upravit' : 'Přidat')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onSuccess[] = [$this, 'onSuccess'];

        if ($this->vehicle)
        {
            $form->setDefaults($this->vehicle->toArray());
        }

        return $form;
    }

    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        if (!$this->vehicle)
        {
            $vehicle = new Vehicle();
        } else {
            $vehicle = $this->vehicle;
        }

        $vehicle->spz = $values->spz;
        $vehicle->vehicleBrand = $values->vehicleBrand;
        $vehicle->vehicleModel = $values->vehicleModel;
        $vehicle->vehicleColor = $values->vehicleColor;
        $vehicle->startKm = $values->startKm;
        $vehicle->endKm = $values->endKm;
        $vehicle->type = $values->type;
        $vehicle->createdAt = new \DateTimeImmutable();

        $this->orm->persistAndFlush($vehicle);

        if ($this->vehicle) {
            $this->getPresenter()->flashMessage('Vozidlo bylo upraveno');
        } else {
            $this->getPresenter()->flashMessage('Vozidlo bylo přidáno');
        }

        $this->getPresenter()->redirect('edit', ['id' => $vehicle->id]);
    }
}