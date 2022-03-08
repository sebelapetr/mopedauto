<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:22
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Forms\IUserFormFactory;
use App\Model\Enum\FlashMessages;
use App\Model\User;
use Nette\ComponentModel\IComponent;

class UsersPresenter extends BaseAppPresenter
{
    public ?User $editUser;

    /** @inject  */
    public IUserFormFactory $userFormFactory;

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->roles = $this->orm->roles->findAll();
    }

    public function renderDefault(int $roleId = null)
    {
        if ($roleId) {
            $role = $this->orm->roles->getById($roleId);
            $this->template->role = $role;
            $this->template->users = $this->orm->users->findBy(['role' => $role])->orderBy('active', 'DESC')->orderBy('name');
        } else {
            $this->template->users = $this->orm->users->findAll()->orderBy('active', 'DESC')->orderBy('name');
        }
    }

    public function actionDetail(int $id): void
    {
        $this->editUser = $this->orm->users->getById($id);
    }

    public function renderDetail(): void
    {
        $this->template->item = $this->editUser;
    }


    public function actionEdit(int $id = null): void
    {
        $this->editUser = $this->orm->users->getById($id);
    }

    public function renderEdit(): void
    {
        $this->template->item = $this->editUser;
    }

    public function handleToggleActive(): void
    {
        $this->editUser->active = $this->editUser->active ? false : true;
        $this->orm->persistAndFlush($this->editUser);
        $this->flashMessage('Uživatel byl deaktivovaný.', FlashMessages::SUCCESS);
        $this->redirect('this');
    }

    public function createComponentUserForm(string $name): ?IComponent
    {
        return $this->userFormFactory->create($this->editUser);
    }

}