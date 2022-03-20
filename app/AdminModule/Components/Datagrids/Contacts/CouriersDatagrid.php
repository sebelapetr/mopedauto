<?php
declare(strict_types=1);

namespace App\AdminModule\Components\Datagrids;

use App\Model\Document;
use App\Model\Orm;
use App\Model\Partner;
use App\Model\Role;
use Nette;
use Nette\Utils\Html;
use Tracy\Debugger;

class CouriersDatagrid extends BasicDatagrid
{
    protected Orm $orm;
    private bool $active;

    public function __construct(Orm $orm, bool $active, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);

        $this->orm = $orm;
        $this->active = $active;
    }

    public function setup(): void
    {
        $domain = "entity.user";

        $this->setDataSource($this->orm->users->findBy(['workingNow' => $this->active, 'role->intName' => Role::INT_NAME_COURIER]));

        $this->addColumnText("name", $domain.".name")
            ->setSortable()
            ->setFilterSelect($this->orm->users->findBy(['role->intName' => Role::INT_NAME_COURIER])->fetchPairs('id', 'name'));

        $this->addColumnText("surname", $domain.".surname")
            ->setSortable()
            ->setFilterSelect($this->orm->users->findBy(['role->intName' => Role::INT_NAME_COURIER])->fetchPairs('id', 'surname'));

        $this->addColumnText("phoneNumber", $domain.".phoneNumber")
            ->setSortable()
            ->setFilterText();

        $this->addAction('detail', 'Detail')
            ->setClass('btn btn-sm btn-success');
    }
}