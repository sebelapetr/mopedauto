<?php
declare(strict_types=1);

namespace App\AppModule\Components;

use App\AppModule\Components\Datagrids\DocumentsDatagrid;
use App\Model\Orm;
use App\Model\Partner;
use Contributte\Translation\Translator;

class DocumentsDatagridFactory
{
    private Translator $translator;

    private Orm $orm;

    public function __construct(Orm $orm, Translator $translator)
    {
        $this->translator = $translator;
        $this->orm = $orm;
    }

    public function create(?Partner $partner): DocumentsDatagrid
    {
        $datagrid = new DocumentsDatagrid($this->orm, $partner);
        $datagrid->setTranslator($this->translator);
        $datagrid->setup();

        return $datagrid;
    }
}