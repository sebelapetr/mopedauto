<?php
declare(strict_types=1);

namespace App\AppModule\Components\Datagrids;

use App\Model\Orm;
use App\Model\Order;
use App\Model\PartnerBranch;
use Nette;
use Nette\Utils\Html;

class BranchOrdersDatagrid extends BasicDatagrid
{
    protected Orm $orm;

    private PartnerBranch $partnerBranch;

    public function __construct(Orm $orm, PartnerBranch $partnerBranch, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);
        $this->orm = $orm;
        $this->partnerBranch = $partnerBranch;
    }

    public function setup(): void
    {
        $domain = "entity.order";

        $this->setDataSource($this->orm->orders->findBy(['partnerBranch' => $this->partnerBranch])->orderBy('createdAt', 'DESC'));

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
            ->setRenderer(function (Order $order) {
                return $order->contactName . ' ' . $order->contactSurname;
            })
            ->setSortable();

        $this->addColumnText("adress", $domain.".address")
            ->setRenderer(function (Order $order) {
                return $order->getAddressString();
            })
            ->setSortable();
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

        $this->addColumnText("email", $domain.".email")
            ->setSortable()
            ->setFilterText();

        $this->addAction('detail', 'Detail')
            ->setClass('btn btn-success btn-sm');
    }
}