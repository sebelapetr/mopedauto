<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:32
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model\Order;
use App\Model\OrderLog;
use App\Model\OrderState;
use App\Model\Role;
use Nextras\Orm\Collection\ICollection;

class DashboardPresenter extends BaseAppPresenter
{
    public function renderDefault()
    {
        $now = new \DateTimeImmutable();
        $this->template->turnover = $this->orm->orders->getDayTurnover($now);
        $this->template->ordersQuantity = $this->orm->orders->getDayOrdersSold($now);
        $this->template->productsQuantity = $this->orm->orders->getDayProductsSold($now);

        $this->template->activeUsers = $this->orm->users->findBy(['workingNow' => true]);
        $this->template->couriersOnWay = [];//$this->getCouriersOnWay();
        $this->template->actualOrders = $this->orm->orders->findAllFinished([])->orderBy('createdAt', 'DESC')->limitBy(50);
    }

}