<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:23
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Datagrids\CouriersDatagrid;
use App\AdminModule\Components\CouriersDatagridFactory;
use App\Model\User;

class ContactsPresenter extends BaseAppPresenter
{
    public bool $active;

    public User $courierUser;

    /** @inject  */
    public CouriersDatagridFactory $couriersDatagridFactory;

    public function actionDefault(bool $active = true): void
    {
        $this->active = $active;
    }

    public function renderDefault(): void
    {
        $this->template->active = $this->active;
    }

    public function createComponentCouriersDatagrid(string $name): CouriersDatagrid
    {
        return $this->couriersDatagridFactory->create($this->active);
    }

    public function actionDetail(int $id): void
    {
        $this->courierUser = $this->orm->users->getById($id);
    }

    public function renderDetail(): void
    {
        $this->template->item = $this->courierUser;
    }
}