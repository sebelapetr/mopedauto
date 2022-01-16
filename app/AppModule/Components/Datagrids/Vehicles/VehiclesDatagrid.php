<?php
declare(strict_types=1);

namespace App\AppModule\Components\Datagrids;

use App\Model\Orm;
use App\Model\Vehicle;
use Model\Enum\ColorsEnum;
use Model\Enum\VehicleBrandsEnum;
use Nette;

class VehiclesDatagrid extends BasicDatagrid
{
    protected Orm $orm;

    public function __construct(Orm $orm, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);
        $this->orm = $orm;
    }

    public function setup(): void
    {
        $domain = "entity.vehicle";

        $this->setDataSource($this->orm->vehicles->findActive());

        $this->addColumnText('id', 'common.id')
            ->setSortable();

        $this->addColumnText("spz", $domain.".spz")
            ->setRenderer(function (Vehicle $item) {
                return substr_replace($item->spz," ", 3, -strlen($item->spz));
            })
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("type", $domain.".type")
            ->setRenderer(function (Vehicle $item) {
                return $this->translator->translate("entity.vehicle.vehicleType.".$item->type);
            })
            ->setSortable()
            ->setFilterSelect([Vehicle::TYPE_CAR => 'Auto', Vehicle::TYPE_SCOOTER => 'Skůtr']);

        $this->addColumnText("vehicleBrand", $domain.".vehicleBrand")
            ->setSortable()
            ->setFilterMultiSelect(VehicleBrandsEnum::getEnum());

        $this->addColumnText("vehicleModel", $domain.".vehicleModel")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("vehicleColor", $domain.".vehicleColor")
            ->setRenderer(function (Vehicle $item) {
                return $this->translator->translate('colors.'.$item->vehicleColor);
            })
            ->setSortable()
            ->setFilterMultiSelect(ColorsEnum::getEnum());

        $this->addColumnText("startKm", $domain.".startKm")
            ->setRenderer(function (Vehicle $item) {
                return number_format($item->startKm, 0,'.',' ') . ' km';
            })
            ->setSortable()
            ->setFilterRange();

        $this->addColumnText("endKm", $domain.".endKm")
            ->setRenderer(function (Vehicle $item) {
                return number_format($item->endKm, 0,'.',' ') . ' km';
            })
            ->setSortable()
            ->setFilterRange();

        $this->addColumnText("createdAt", $domain.".createdAt")
            ->setRenderer(function (Vehicle $item) {
                return $item->createdAt->format('d.m.Y');
            })
            ->setSortable()
            ->setFilterDate();

        $this->addAction('edit', 'common.edit', 'edit')
            ->setClass('btn btn-sm btn-success');

        $this->addAction('delete', 'common.delete', 'delete!')
            ->setClass('btn btn-sm btn-danger ajax');

    }
}