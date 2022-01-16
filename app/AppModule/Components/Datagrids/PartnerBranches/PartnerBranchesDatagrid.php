<?php
declare(strict_types=1);

namespace App\AppModule\Components\Datagrids;

use App\Model\Orm;
use App\Model\Partner;
use App\Model\PartnerBranch;
use App\Model\Role;
use Nette;
use Nette\Utils\Html;

class PartnerBranchesDatagrid extends BasicDatagrid
{
    protected Orm $orm;
    private ?Partner $partner;

    public function __construct(Orm $orm, ?Partner $partner, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);

        $this->orm = $orm;
        $this->partner = $partner;
    }

    public function setup(): void
    {
        $domain = "entity.partnerBranch";

        if ($this->partner)
            $this->setDataSource($this->orm->partnersBranches->findByPartner($this->partner));
        else
            $this->setDataSource($this->orm->partnersBranches->findAll());

        $this->addColumnText('id', 'common.id')
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("name", $domain.".name")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("email", $domain.".email")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("phoneNumber", $domain.".phoneNumber")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("createdAt", $domain.".createdAt")
            ->setRenderer(function (PartnerBranch $item) {
                return $item->createdAt->format('d.m.Y');
            })
            ->setSortable()
            ->setFilterDate();

        $this->addColumnText("partner", $domain.".partner")
            ->setSortable()
            ->setRenderer(function (PartnerBranch $item) {
                return Html::el('a', [
                    'href' => $this->getPresenter()->link('Partners:detail', ['id' => $item->partner->id]),
                    'target' => '_blank'
                ])->setText($item->partner->name);
            })
            ->setFilterSelect([null => '']+$this->orm->partners->findAll()->fetchPairs('id', 'name'));

        $this->addColumnText("branchUser", $domain.".branchUser")
            ->setSortable()
            ->setRenderer(function (PartnerBranch $item) {
                return Html::el('a', [
                    'href' => $this->getPresenter()->link('Users:detail', ['id' => $item->branchUser->id]),
                    'target' => '_blank'
                ])->setText($item->branchUser ? $item->branchUser->name : 'common.notAssigned');
            })
            ->setFilterSelect([null => '']+$this->orm->users->findBy(['role->intName' => Role::INT_NAME_BRANCH])->fetchPairs('id', 'name'));

        $this->addColumnText('addressNumber', $domain.'.addressNumber')
            ->setSortable()
            ->setFilterText();

        $this->addColumnText('street', $domain.'.street')
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("city", $domain.".city")
            ->setRenderer(function (PartnerBranch $partnerBranch) {
                return $partnerBranch->city->name;
            })
            ->setSortable()
            ->setFilterMultiSelect($this->orm->cities->findAll()->fetchPairs('id', 'name'));

        $this->addColumnText("zip", $domain.".zip")
            ->setRenderer(function (PartnerBranch $partnerBranch) {
                return $partnerBranch->zip->zip;
            })
            ->setSortable()
            ->setFilterMultiSelect($this->orm->zips->findAll()->fetchPairs('id', 'zip'));

        $this->addAction('Branches:detail', 'Detail')
            ->setClass('btn btn-success btn-sm');

        $this->addAction('Branches:edit', 'common.edit')
            ->setClass('btn btn-primary btn-sm');

    }
}