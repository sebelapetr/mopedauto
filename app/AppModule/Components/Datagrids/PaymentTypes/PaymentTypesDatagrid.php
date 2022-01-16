<?php
declare(strict_types=1);

namespace App\AppModule\Components\Datagrids;

use App\Model\Orm;
use Nette;

class PaymentTypesDatagrid extends BaseDatagrid
{
    protected Orm $orm;

    public function __construct(Orm $orm, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);
        $this->orm = $orm;
    }

    public function setup(): void
    {
        $domain = "entity.paymentType";

        $this->setDataSource($this->orm->paymentTypes->findAll());

        $this->addColumnText('id', 'common.id')
            ->setSortable();

        $this->addColumnText("type", $domain.".type")
            ->setSortable();

        $this->addColumnText("description", $domain.".description")
            ->setSortable();

        $this->addAction('edit', 'common.edit')
            ->setClass('btn btn-success btn-sm');
    }
}