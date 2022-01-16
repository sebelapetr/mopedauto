<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:22
 */

declare(strict_types=1);

namespace App\AppModule\Presenters;

use App\AppModule\Components\Datagrids\OrdersDatagrid;
use App\AppModule\Components\OrdersDatagridFactory;

class OrdersPresenter extends BaseAppPresenter
{
    /** @inject */
    public OrdersDatagridFactory $ordersDatagridFactory;

    public function createComponentOrdersDatagrid(string $name): OrdersDatagrid
    {
        return $this->ordersDatagridFactory->create();
    }
}