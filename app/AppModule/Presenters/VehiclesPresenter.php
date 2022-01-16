<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:23
 */

declare(strict_types=1);

namespace App\AppModule\Presenters;

use App\AppModule\Components\Datagrids\VehiclesDatagrid;
use App\AppModule\Components\VehiclesDatagridFactory;
use App\AppModule\Forms\IVehicleFormFactory;
use App\AppModule\Forms\VehicleForm;
use App\Model\Vehicle;
use Tracy\Debugger;

class VehiclesPresenter extends BaseAppPresenter
{

    /** @inject */
    public VehiclesDatagridFactory $vehiclesDatagridFactory;

    /** @inject */
    public IVehicleFormFactory $vehicleFormFactory;

    public ?Vehicle $vehicle;

    public function actionEdit(int $id = null): void
    {
        if ($id) {
            try {
                $this->vehicle = $this->orm->vehicles->getById($id);
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }
        } else {
            $this->vehicle = null;
        }
    }

    public function renderEdit(): void
    {
        $this->template->item = $this->vehicle;
    }

    public function createComponentVehiclesDatagrid(string $name): VehiclesDatagrid
    {
        return $this->vehiclesDatagridFactory->create();
    }

    public function createComponentVehicleForm(): VehicleForm
    {
        return $this->vehicleFormFactory->create($this->vehicle);
    }

    public function handleDelete(int $id): void
    {
        $item = $this->orm->vehicles->getById($id);

        if ($item)
        {
            $item->deleted = true;
            $this->orm->persistAndFlush($item);
        }

        if ($this->isAjax()) {
            $this['vehiclesDatagrid']->reload();
        } else {
            $this->redirect('this');
        }
    }
}