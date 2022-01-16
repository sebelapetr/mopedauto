<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:23
 */

declare(strict_types=1);

namespace App\AppModule\Presenters;

use App\AppModule\Components\Datagrids\PaymentTypesDatagrid;
use App\AppModule\Components\PaymentTypesDatagridFactory;
use App\AppModule\Forms\IPaymentTypeFormFactory;
use App\AppModule\Forms\PaymentTypeForm;
use App\Model\PaymentType;
use Tracy\Debugger;

class PaymentTypesPresenter extends BaseAppPresenter
{
    /** @inject */
    public PaymentTypesDatagridFactory $paymentTypesDatagridFactory;

    /** @inject */
    public IPaymentTypeFormFactory $paymentTypeFormFactory;

    public ?PaymentType $paymentType;

    public function actionEdit(int $id = null): void
    {
        if ($id) {
            try {
                $this->paymentType = $this->orm->paymentTypes->getById($id);
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }
        } else {
            $this->paymentType = null;
        }
    }

    public function renderEdit(): void
    {
        $this->template->item = $this->paymentType;
    }

    public function createComponentPaymentTypesDatagrid(string $name): PaymentTypesDatagrid
    {
        return $this->paymentTypesDatagridFactory->create();
    }

    public function createComponentPaymentTypeForm(): PaymentTypeForm
    {
        return $this->paymentTypeFormFactory->create($this->paymentType);
    }
}