<?php
declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Forms\IEditRightsFormFactory;
use App\Model\Role;
use Nette\ComponentModel\IComponent;

class RightsPresenter extends BaseAppPresenter {

    public Role $editRole;

    /** @inject */
    public IEditRightsFormFactory $editRightsFormFactory;

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->roles = $this->orm->roles->findAll();
    }


    public function actionDefault()
    {

    }

    public function renderDefault()
    {

    }

    public function actionEdit(int $id)
    {
        $this->editRole = $this->orm->roles->getById($id);
    }

    public function renderEdit()
    {
        $this->template->editRole = $this->editRole;
    }

    public function createComponentEditRightsForm(string $name): ?IComponent
    {
        return $this->editRightsFormFactory->create($this->editRole);
    }

}