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

        $this->setDataSource($this->orm->orders->findAllFinished([]));

        $this->setColumnsHideable();

        $this->addColumnText('id', 'Číslo objednávky')
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("createdAt", "Datum")
            ->setRenderer(function (Order $item) {
                return $item->createdAt->format('d.m.Y H:i');
            })
            ->setSortable()
            ->setFilterDate();

        $this->addColumnText("name", "Jméno")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("surname", $domain.".contactSurname")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("totalPriceVat", $domain.".totalPriceVat")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("state", $domain.".state")
            ->setRenderer(function(Order $order) use ($domain) {
                return $this->translator->translate($domain.'.state_'.$order->state);
            })
            ->setSortable()
            ->setFilterSelect([
                Order::ORDER_STATE_CREATED => $this->translator->translate($domain.'.state_'.Order::ORDER_STATE_CREATED),
                Order::ORDER_STATE_RECEIVED => $this->translator->translate($domain.'.state_'.Order::ORDER_STATE_RECEIVED),
            ])->setPrompt('');

        $this->addAction('detail', 'Detail')
            ->setClass('btn btn-success btn-sm');
    }
}