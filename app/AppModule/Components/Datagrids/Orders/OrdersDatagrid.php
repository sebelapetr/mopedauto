<?php
declare(strict_types=1);

namespace App\AppModule\Components\Datagrids;

use App\Model\Orm;
use App\Model\Order;
use Nette;
use Nette\Utils\Html;

class OrdersDatagrid extends BasicDatagrid
{
    protected Orm $orm;

    public function __construct(Orm $orm, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);
        $this->orm = $orm;
    }

    public function setup(): void
    {
        $domain = "entity.order";

        $this->setDataSource($this->orm->orders->findAll()->orderBy('createdAt', 'DESC'));

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

        $this->addColumnText("addressNumber", $domain.".addressNumber")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("street", $domain.".street")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("city", $domain.".city")
            ->setRenderer(function (Order $order) {
                return $order->city->name;
            })
            ->setSortable()
            ->setFilterMultiSelect($this->orm->cities->findAll()->fetchPairs('id', 'name'));

        $this->addColumnText("zip", $domain.".zip")
            ->setRenderer(function (Order $order) {
                return $order->zip->zip;
            })
            ->setSortable()
            ->setDefaultHide()
            ->setFilterMultiSelect($this->orm->zips->findAll()->fetchPairs('id', 'zip'));

        $this->addColumnText("email", $domain.".email")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("ic", $domain.".ic")
            ->setDefaultHide()
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("dic", $domain.".dic")
            ->setSortable()
            ->setDefaultHide()
            ->setFilterText();

        $this->addColumnText("nameInvoicing", $domain.".nameInvoicing")
            ->setDefaultHide()
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("surnameInvoicing", $domain.".surnameInvoicing")
            ->setDefaultHide()
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("streetInvoicing", $domain.".streetInvoicing")
            ->setDefaultHide()
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("invoicingCity", $domain.".invoicingCity")
            ->setRenderer(function (Order $order) {
                return $order->invoicingCity->name;
            })
            ->setSortable()
            ->setFilterMultiSelect($this->orm->cities->findAll()->fetchPairs('id', 'name'));

        $this->addColumnText("invoicingZip", $domain.".invoicingZip")
            ->setRenderer(function (Order $order) {
                return $order->invoicingZip->zip;
            })
            ->setSortable()
            ->setDefaultHide()
            ->setFilterMultiSelect($this->orm->zips->findAll()->fetchPairs('id', 'zip'));

        $this->addColumnText("remark", $domain.".remark")
            ->setDefaultHide()
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("orderSource", $domain.".orderSource")
            ->setDefaultHide()
            ->setSortable()
            ->setFilterText();

        $this->addAction('detail', 'Detail')
            ->setClass('btn btn-success btn-sm');
    }
}