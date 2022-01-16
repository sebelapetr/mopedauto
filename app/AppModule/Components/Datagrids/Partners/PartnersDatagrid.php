<?php
declare(strict_types=1);

namespace App\AppModule\Components\Datagrids;

use App\Model\Orm;
use App\Model\Partner;
use App\Model\Role;
use Nette;
use Nette\Utils\Html;

class PartnersDatagrid extends BasicDatagrid
{
    protected Orm $orm;

    public function __construct(Orm $orm, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);
        $this->orm = $orm;
    }

    public function setup(): void
    {
        $domain = "entity.partner";

        $this->setDataSource($this->orm->partners->findAll());

        $this->addColumnText('id', 'common.id')
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("name", $domain.".name")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("partnerFrom", $domain.".partnerFrom")
            ->setRenderer(function (Partner $item) {
                return $item->partnerFrom->format('d.m.Y');
            })
            ->setSortable()
            ->setFilterDate();

        $this->addColumnText("email", $domain.".email")
            ->setSortable()
            ->setFilterText();


        $this->addColumnText("phoneNumber", $domain.".phoneNumber")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("adminUser", $domain.".adminUser")
            ->setSortable()
            ->setSortable()
            ->setRenderer(function (Partner $item) {
                return Html::el('a', [
                    'href' => $this->getPresenter()->link('Users:detail', ['id' => $item->adminUser->id]),
                    'target' => '_blank'
                ])->setText($item->adminUser ? $item->adminUser->name : 'common.notAssigned');
            })
            ->setFilterSelect([null => '']+$this->orm->users->findBy(['role->intName' => Role::INT_NAME_PARTNER])->fetchPairs('id', 'name'));

        $this->addColumnText("ico", $domain.".ico")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("dic", $domain.".dic")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("createdAt", $domain.".createdAt")
            ->setRenderer(function (Partner $item) {
                return $item->createdAt->format('d.m.Y');
            })
            ->setSortable()
            ->setFilterDate();

        $this->addAction('detail', 'Detail')
            ->setClass('btn btn-success btn-sm');

        $this->addAction('edit', 'common.edit')
            ->setClass('btn btn-primary btn-sm');

    }
}