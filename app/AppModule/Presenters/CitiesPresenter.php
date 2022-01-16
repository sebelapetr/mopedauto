<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:23
 */

declare(strict_types=1);

namespace App\AppModule\Presenters;

use App\AppModule\Components\CitiesDatagridFactory;
use App\AppModule\Components\Datagrids\CitiesDatagrid;
use App\AppModule\Forms\CityForm;
use App\AppModule\Forms\ICityFormFactory;
use App\Model\City;
use Tracy\Debugger;

class CitiesPresenter extends BaseAppPresenter
{

    /** @inject */
    public CitiesDatagridFactory $citiesDatagridFactory;

    /** @inject */
    public ICityFormFactory $cityFormFactory;

    public ?City $city;

    public function actionEdit(int $id = null): void
    {
        if ($id) {
            try {
                $this->city = $this->orm->cities->getById($id);
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }
        } else {
            $this->city = null;
        }
    }

    public function renderEdit(): void
    {
        $this->template->item = $this->city;
    }

    public function createComponentCitiesDatagrid(string $name): CitiesDatagrid
    {
        return $this->citiesDatagridFactory->create();
    }

    public function createComponentCityForm(): CityForm
    {
        return $this->cityFormFactory->create($this->city);
    }
}