<?php
declare(strict_types=1);

namespace App\AppModule\Components;

use App\AppModule\Components\Datagrids\ZipsDatagrid;
use App\Model\Orm;
use Contributte\Translation\Translator;

class ZipsDatagridFactory
{
    private Translator $translator;

    private Orm $orm;

    public function __construct(Orm $orm, Translator $translator)
    {
        $this->translator = $translator;
        $this->orm = $orm;
    }

    public function create(): ZipsDatagrid
    {
        $datagrid = new ZipsDatagrid($this->orm);
        $datagrid->setTranslator($this->translator);
        $datagrid->setup();

        return $datagrid;
    }
}