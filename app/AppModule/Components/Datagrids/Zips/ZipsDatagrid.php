<?php
declare(strict_types=1);

namespace App\AppModule\Components\Datagrids;

use App\Model\Orm;
use App\Model\Zip;
use Nette;

class ZipsDatagrid extends BasicDatagrid
{
    protected Orm $orm;

    public function __construct(Orm $orm, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);
        $this->orm = $orm;
    }

    public function setup(): void
    {
        $domain = "entity.zip";

        $this->setDataSource($this->orm->zips->findActive());

        $this->addColumnText('id', 'common.id')
            ->setSortable();

        $this->addColumnText("zip", $domain.".zip")
            ->setSortable();

        $this->addColumnText('city', $domain.".city")
            ->setRenderer(function (Zip $zip) {
                return $zip->city->name;
            })
            ->setFilterMultiSelect($this->orm->cities->findAll()->fetchPairs('id', 'name'));

        $this->addAction('edit', "common.edit")
            ->setClass('btn btn-sm btn-success');

        $this->addAction('delete', "common.delete", 'delete!')
            ->setClass('btn btn-sm btn-danger ajax');
    }
}