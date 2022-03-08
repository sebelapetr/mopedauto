<?php
declare(strict_types=1);

namespace App\AdminModule\Components;

use App\AdminModule\Components\Datagrids\OrdersDatagrid;
use App\Model\Orm;
use Contributte\Translation\Translator;
use Nette\Application\UI\Presenter;

class OrdersDatagridFactory
{

    private Translator $translator;

    private Orm $orm;

    public function __construct(Orm $orm, Translator $translator)
    {
        $this->translator = $translator;
        $this->orm = $orm;
    }

    public function create(Presenter $presenter): OrdersDatagrid
    {
        $datagrid = new OrdersDatagrid($this->orm, $presenter);
        $datagrid->setTranslator($this->translator);
        $datagrid->setup();

        return $datagrid;
    }
}