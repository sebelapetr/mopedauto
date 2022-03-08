<?php
declare(strict_types=1);

namespace App\AdminModule\Components;

use App\AdminModule\Components\Datagrids\PartnersDatagrid;
use App\AdminModule\Components\Datagrids\ProductsDatagrid;
use App\Model\Orm;
use Contributte\Translation\Translator;

class ProductsDatagridFactory
{
    private Translator $translator;

    private Orm $orm;

    public function __construct(Orm $orm, Translator $translator)
    {
        $this->translator = $translator;
        $this->orm = $orm;
    }

    public function create(): ProductsDatagrid
    {
        $datagrid = new ProductsDatagrid($this->orm);
        $datagrid->setTranslator($this->translator);
        $datagrid->setup();

        return $datagrid;
    }
}