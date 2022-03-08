<?php
declare(strict_types=1);

namespace App\AdminModule\Components;

use App\AdminModule\Components\Datagrids\CouriersDatagrid;
use App\Model\Orm;
use App\Model\Partner;
use Contributte\Translation\Translator;

class CouriersDatagridFactory
{
    private Translator $translator;

    private Orm $orm;

    public function __construct(Orm $orm, Translator $translator)
    {
        $this->translator = $translator;
        $this->orm = $orm;
    }

    public function create(bool $active): CouriersDatagrid
    {
        $datagrid = new CouriersDatagrid($this->orm, $active);
        $datagrid->setTranslator($this->translator);
        $datagrid->setup();

        return $datagrid;
    }
}