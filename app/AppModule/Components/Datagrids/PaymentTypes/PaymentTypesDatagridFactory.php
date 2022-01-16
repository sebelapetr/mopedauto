<?php
declare(strict_types=1);

namespace App\AppModule\Components;

use App\AppModule\Components\Datagrids\PaymentTypesDatagrid;
use App\Model\Orm;
use Contributte\Translation\Translator;

class PaymentTypesDatagridFactory
{
    private Translator $translator;

    private Orm $orm;

    public function __construct(Orm $orm, Translator $translator)
    {
        $this->translator = $translator;
        $this->orm = $orm;
    }

    public function create(): PaymentTypesDatagrid
    {
        $datagrid = new PaymentTypesDatagrid($this->orm);
        $datagrid->setTranslator($this->translator);
        $datagrid->setup();

        return $datagrid;
    }
}