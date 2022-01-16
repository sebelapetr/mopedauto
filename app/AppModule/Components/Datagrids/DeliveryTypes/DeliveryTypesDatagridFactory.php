<?php
declare(strict_types=1);

namespace App\AppModule\Components;

use App\AppModule\Components\Datagrids\DeliveryTypesDatagrid;
use App\Model\Orm;
use Contributte\Translation\Translator;

class DeliveryTypesDatagridFactory
{
    private Translator $translator;

    private Orm $orm;

    public function __construct(Orm $orm, Translator $translator)
    {
        $this->translator = $translator;
        $this->orm = $orm;
    }

    public function create(): DeliveryTypesDatagrid
    {
        $datagrid = new DeliveryTypesDatagrid($this->orm);
        $datagrid->setTranslator($this->translator);
        $datagrid->setup();

        return $datagrid;
    }
}