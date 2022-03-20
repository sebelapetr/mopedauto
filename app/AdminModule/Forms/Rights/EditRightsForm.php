<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Enum\FlashMessages;
use App\Model\Orm;
use App\Model\PaymentType;
use App\Model\Role;
use Model\Enum\PaymentTypeEnum;
use Nette;
use Tracy\Debugger;

class EditRightsForm extends Nette\Application\UI\Control
{
    private Orm $orm;
    public Role $role;

    public function __construct(Orm $orm, Role $role)
    {
        $this->orm = $orm;
        $this->role = $role;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/EditRightsForm.latte');
        $this->template->role = $this->role;
        $this->template->rights = $this->orm->rights->findAll();
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addCheckboxList('allowedActions', null, $this->orm->actions->findAll()->fetchPairs('id', 'name'));

        $form->addSubmit('send', 'Upravit')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onSuccess[] = [$this, 'onSuccess'];

        $form->setDefaults(['allowedActions' => $this->role->actions->toCollection()->fetchPairs(null, 'id')]);

        return $form;
    }

    public function onSuccess(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values): void
    {
        foreach ($this->role->actions as $action) {
            $this->role->actions->remove($action);
        }

        foreach ($values->allowedActions as $actionId)
        {
            $this->role->actions->add($this->orm->actions->getById($actionId));
        }

        $this->orm->persistAndFlush($this->role);

        $this->getPresenter()->flashMessage('Role byla uložena.', FlashMessages::SUCCESS);

        $this->getPresenter()->redirect('edit', ['id' => $this->role->id]);
    }
}