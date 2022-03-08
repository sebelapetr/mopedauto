<?php
declare(strict_types=1);

namespace App\AdminModule\Components\Datagrids;

use App\Model\DeliveryType;
use App\Model\Enum\FlashMessages;
use App\Model\OrderState;
use App\Model\Orm;
use App\Model\Order;
use App\Model\PaymentType;
use App\Model\Role;
use App\Model\User;
use Nette;
use Nette\Utils\Html;

class OrdersDatagrid extends BasicDatagrid
{
    protected Orm $orm;
    protected Nette\Application\UI\Presenter $presenter;

    public function __construct(Orm $orm, Nette\Application\UI\Presenter $presenter, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);
        $this->orm = $orm;
        $this->presenter = $presenter;
    }

    public function setup(): void
    {
        $domain = "entity.order";

        $this->setDataSource($this->orm->orders->findAll());

        $this->setColumnsHideable();

        $this->addColumnText('id', 'common.id')
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("createdAt", $domain.".createdAt")
            ->setRenderer(function (Order $item) {
                return $item->createdAt->format('d.m.Y H:i');
            })
            ->setSortable()
            ->setFilterDate();

        $this->addColumnText("name", $domain.".contactName")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("surname", $domain.".contactSurname")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("totalPriceVat", $domain.".totalPriceVat")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("state", $domain.".state")
            ->setSortable()
            ->setFilterSelect([
                Order::ORDER_STATE_UNFINISHED => Order::ORDER_STATE_UNFINISHED,
                Order::ORDER_STATE_CREATED => Order::ORDER_STATE_CREATED,
                Order::ORDER_STATE_RECEIVED => Order::ORDER_STATE_RECEIVED,
            ]);

        $this->addAction('detail', 'Detail')
            ->setClass('btn btn-success btn-sm');
    }
}