<?php
declare(strict_types=1);

namespace App\AppModule\Components;

use App\AppModule\Components\Datagrids\BranchOrdersDatagrid;
use App\Model\Orm;
use App\Model\PartnerBranch;
use Contributte\Translation\Translator;

class BranchOrdersDatagridFactory
{

    private Translator $translator;

    private Orm $orm;

    public function __construct(Orm $orm, Translator $translator)
    {
        $this->translator = $translator;
        $this->orm = $orm;
    }

    public function create(PartnerBranch $partnerBranch): BranchOrdersDatagrid
    {
        $datagrid = new BranchOrdersDatagrid($this->orm, $partnerBranch);
        $datagrid->setTranslator($this->translator);
        $datagrid->setup();

        return $datagrid;
    }
}