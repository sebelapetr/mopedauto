<?php
declare(strict_types=1);

namespace App\AppModule\Components\Datagrids;

use App\Model\Orm;
use App\Model\Order;
use App\Model\Partner;
use Nette;
use Nette\Utils\Html;

class PartnerOrdersDatagrid extends BasicDatagrid
{
    protected Orm $orm;

    private Partner $partner;

    public function __construct(Partner $partner, Orm $orm, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);
        $this->orm = $orm;
        $this->partner = $partner;
    }

    public function setup(): void
    {
        $domain = "entity.order";

        $this->setDataSource($this->orm->orders->findBy(['partnerBranch->partner' => $this->partner])->orderBy('createdAt', 'DESC'));

        $this->setColumnsHideable();

        $this->addColumnText('id', 'common.id')
            ->setSortable()
            ->setDefaultHide()
            ->setFilterText();

        $this->addColumnText("createdAt", $domain.".createdAt")
            ->setRenderer(function (Order $item) {
                return $item->createdAt->format('d.m.Y H:i');
            })
            ->setSortable()
            ->setFilterDate();

        $this->addColumnText("eanCode", $domain.".eanCode")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("deliveryDate", $domain.".deliveryDate")
            ->setRenderer(function (Order $item) {
                return $item->deliveryDate->format('d.m.Y H:i');
            })
            ->setSortable()
            ->setFilterDate();

        $this->addColumnText("phoneNumber", $domain.".phoneNumber")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("contactName", $domain.".contactName")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("contactSurname", $domain.".contactSurname")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("partnerBranch", $domain.".partnerBranch")
            ->setSortable()
            ->setRenderer(function (Order $item) {
                return Html::el('a', [
                    'href' => $this->getPresenter()->link('Branches:detail', ['id' => $item->partnerBranch->id]),
                    'target' => '_blank'
                ])->setText($item->partnerBranch ? $item->partnerBranch->name : 'common.notAssigned');
            })
            ->setFilterSelect([null => '']+$this->orm->partnersBranches->findAll()->fetchPairs('id', 'name'));

        /*
        todo payment type
        $this->addColumnText("paymentType", $domain.".paymentType")
            ->setSortable()
            ->setFilterText();

        todo delivery type
        $this->addColumnText("deliveryType", $domain.".deliveryType")
            ->setSortable()
            ->setFilterText();
        */

        $this->addColumnText("city", $domain.".city")
            ->setRenderer(function (Order $order) {
                return $order->city->name;
            })
            ->setSortable()
            ->setFilterMultiSelect($this->orm->cities->findAll()->fetchPairs('id', 'name'));

        $this->addColumnText("email", $domain.".email")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("ic", $domain.".ic")
            ->setSortable()
            ->setFilterText();


        $this->addAction('detail', 'Detail')
            ->setClass('btn btn-success btn-sm');
    }
}