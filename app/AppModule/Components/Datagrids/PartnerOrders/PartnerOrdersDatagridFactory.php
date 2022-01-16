<?php
declare(strict_types=1);

namespace App\AppModule\Components;

use App\AppModule\Components\Datagrids\PartnerOrdersDatagrid;
use App\Model\Orm;
use App\Model\Partner;
use Contributte\Translation\Translator;

class PartnerOrdersDatagridFactory
{

    private Translator $translator;

    private Orm $orm;

    public function __construct(Orm $orm, Translator $translator)
    {
        $this->translator = $translator;
        $this->orm = $orm;
    }

    public function create(Partner $partner): PartnerOrdersDatagrid
    {
        $datagrid = new PartnerOrdersDatagrid($partner, $this->orm);
        $datagrid->setTranslator($this->translator);
        $datagrid->setup();

        return $datagrid;
    }
}