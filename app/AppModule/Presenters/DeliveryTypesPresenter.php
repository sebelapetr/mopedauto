<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:23
 */

declare(strict_types=1);

namespace App\AppModule\Presenters;

use App\AppModule\Components\Datagrids\DeliveryTypesDatagrid;
use App\AppModule\Components\DeliveryTypesDatagridFactory;
use App\AppModule\Forms\DeliveryTypeForm;
use App\AppModule\Forms\IDeliveryTypeFormFactory;
use App\Model\DeliveryType;
use Tracy\Debugger;

class DeliveryTypesPresenter extends BaseAppPresenter
{
    /** @inject */
    public DeliveryTypesDatagridFactory $deliveryTypesDatagridFactory;

    /** @inject */
    public IDeliveryTypeFormFactory $deliveryTypeFormFactory;

    public ?DeliveryType $deliveryType;

    public function actionEdit(int $id = null): void
    {
        if ($id) {
            try {
                $this->deliveryType = $this->orm->deliveryTypes->getById($id);
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }
        } else {
            $this->deliveryType = null;
        }
    }

    public function renderEdit(): void
    {
        $this->template->item = $this->deliveryType;
    }

    public function createComponentDeliveryTypesDatagrid(string $name): DeliveryTypesDatagrid
    {
        return $this->deliveryTypesDatagridFactory->create();
    }

    public function createComponentDeliveryTypeForm(): DeliveryTypeForm
    {
        return $this->deliveryTypeFormFactory->create($this->deliveryType);
    }
}