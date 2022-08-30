<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:21
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\CarsDatagridFactory;
use App\AdminModule\Components\Datagrids\CarsDatagrid;
use App\AdminModule\Forms\CarForm;
use App\AdminModule\Forms\ICarFormFactory;
use App\AdminModule\Forms\IVehicleFormFactory;
use App\AdminModule\Forms\IVehicleImagesFormFactory;
use App\AdminModule\Forms\VehicleForm;
use App\AdminModule\Forms\VehicleImagesForm;
use App\Model\Vehicle;
use Tracy\Debugger;

class CarsPresenter extends BaseAppPresenter
{
    /** @inject */
    public CarsDatagridFactory $carsDatagridFactory;

    /** @inject */
    public ICarFormFactory $carFormFactory;

    /** @inject */
    public IVehicleImagesFormFactory $carImagesFormFactory;

    public ?Vehicle $actualCar;


    public function actionEdit(int $id = null): void
    {
        if ($id) {
            try {
                $this->actualCar = $this->orm->vehicles->getById($id);
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }
            if (!$this->actualCar) {
                $this->redirect('default');
            }
        } else {
            $this->actualCar = null;
        }
    }

    public function renderEdit(): void
    {
        $this->template->item = $this->actualCar;
    }

    public function actionDetail(int $id): void
    {
        if ($id) {
            try {
                $this->actualCar = $this->orm->vehicles->getById($id);
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }
            if (!$this->actualCar) {
                $this->redirect('default');
            }
        }
    }

    public function renderDetail(): void
    {
        $this->template->item = $this->actualCar;
    }

    public function createComponentCarsDatagrid(string $name): CarsDatagrid
    {
        return $this->carsDatagridFactory->create();
    }

    public function createComponentCarForm(): CarForm
    {
        return $this->carFormFactory->create($this->actualCar);
    }

    public function createComponentCarImagesForm(): VehicleImagesForm
    {
        return $this->carImagesFormFactory->create($this->actualCar);
    }

    public function handleDelete(int $id): void
    {
        $item = $this->orm->vehicles->getById($id);

        if ($item) {
            $item->deleted = true;
            $this->orm->persistAndFlush($item);
        }

        if ($this->isAjax()) {
            $this['carsDatagrid']->reload();
        } else {
            $this->redirect('default');
        }
    }

}