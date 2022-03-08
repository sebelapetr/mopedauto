<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:22
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Datagrids\OrdersDatagrid;
use App\AdminModule\Components\OrdersDatagridFactory;
use App\AdminModule\Forms\AssignCourierForm;
use App\AdminModule\Forms\IAssignCourierFormFactory;
use App\AdminModule\Forms\IOrderFormFactory;
use App\AdminModule\Forms\IOrderStateFormFactory;
use App\AdminModule\Forms\OrderForm;
use App\AdminModule\Forms\OrderStateForm;
use App\Model\Enum\FlashMessages;
use App\Model\Order;
use App\Model\OrderItem;
use App\Model\OrderItemState;
use App\Model\Role;
use App\Services\LabelsService;
use Tracy\Debugger;

class OrdersPresenter extends BaseAppPresenter
{
    /** @inject */
    public OrdersDatagridFactory $ordersDatagridFactory;

    public Order $order;

    /** @inject */
    public IOrderStateFormFactory $orderStateFormFactory;

    /** @inject  */
    public IAssignCourierFormFactory $assignCourierFormFactory;

    /** @inject */
    public IOrderFormFactory $orderFormFactory;

    public function createComponentOrdersDatagrid(string $name): OrdersDatagrid
    {
        return $this->ordersDatagridFactory->create($this);
    }


    public function actionDefault(){
        if ($this->getUser()->isInRole(Role::INT_NAME_COURIER)) {
            $this->flashMessage('Pro tuto akci nemáte oprávnění', FlashMessages::DANGER);
            $this->redirect('Dashboard:default');
            return;
        }
    }

    public function actionDetail(int $id = null): void
    {
        if ($id) {
            try {
                $this->order = $this->orm->orders->getById($id);
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }
        } else {
            $this->order = null;
        }
        if ($this->user->isInRole(Role::INT_NAME_PARTNER))
        {
            if ($this->order->partnerBranch->partner !== $this->user->user->partner)
            {
                $this->flashMessage('K této objednávce nemáte přístup', FlashMessages::DANGER);
                $this->redirect('default');
            }
        }
        if ($this->user->isInRole(Role::INT_NAME_BRANCH))
        {
            if ($this->order->partnerBranch !== $this->user->user->branch)
            {
                $this->flashMessage('K této objednávce nemáte přístup', FlashMessages::DANGER);
                $this->redirect('default');
            }
        }
    }

    public function actionAddItems(int $orderId): void
    {
        $this->order = $this->orm->orders->getById($orderId);
    }

    public function renderAddItems(): void
    {
        $this->template->order = $this->order;
    }

    public function renderDetail(): void
    {
        $this->template->item = $this->order;
    }

    public function createComponentChangeOrderStateForm(): OrderStateForm
    {
        return $this->orderStateFormFactory->create($this->order);
    }

    public function createComponentAssignCourierForm(): AssignCourierForm
    {
        return $this->assignCourierFormFactory->create($this->order);
    }

    public function createComponentOrderForm(): OrderForm
    {
        return $this->orderFormFactory->create();
    }

    public function handleAddOrderItem(): void
    {
        parse_str($_POST['data'], $result);

        $orderItem = new OrderItem();
        $orderItem->order = $this->order;
        foreach ($result as $column => $value)
        {
            if ($value === '' || $value === ' ')
            {
                $value = null;
            }
            $orderItem->$column = $value;
        }

        $vat = $orderItem->vat;
        $vatDivisor = 1 + ($vat / 100);
        $price = $orderItem->brutto;
        $priceBeforeVat = $price / $vatDivisor;
        $netto = number_format($priceBeforeVat, 2);
        $orderItem->netto = $netto;

        $orderItem->orderItemState = $this->orm->orderItemStates->getBy(['type' => OrderItemState::TYPE_OK]);

        $this->orm->persistAndFlush($orderItem);
        $this->redrawControl('orderItems');
        $this->redrawControl('scripts');
    }

    public function handleRemoveOrderItem(): void
    {
        $orderItem = $this->orm->orderItems->getById(intval($_POST['orderItemId']));
        $this->orm->remove($orderItem);
        $this->orm->flush();
        $this->redrawControl('orderItems');
        $this->redrawControl('scripts');
    }

    public function handlePrintLabel(): void
    {
        $this->labelsService->createLabel($this->order);
    }
}