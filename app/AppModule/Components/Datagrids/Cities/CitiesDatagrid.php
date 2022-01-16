<?php
declare(strict_types=1);

namespace App\AppModule\Components\Datagrids;

use App\Model\Orm;
use Nette;

class CitiesDatagrid extends BasicDatagrid
{
    protected Orm $orm;

    public function __construct(Orm $orm, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);
        $this->orm = $orm;
    }

    public function setup(): void
    {
        $domain = "entity.city";

        $this->setDataSource($this->orm->cities->findAll());

        $this->addColumnText('id', $this->translator->translate('common.id'))
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("name", $this->translator->translate($domain.".name"))
            ->setSortable()
            ->setFilterText();

        $this->addAction('edit', $this->translator->translate('common.edit'))
            ->setClass('btn btn-success btn-sm');
    }
}