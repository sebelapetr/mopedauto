<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Enum\FlashMessages;
use App\Model\Orm;
use App\Model\Partner;
use App\Model\Role;
use App\Model\User;
use Contributte\Translation\Translator;
use Nette;

class UserForm extends Nette\Application\UI\Control
{
    private Orm $orm;

    public ?User $user;

    public Translator $translator;

    public function __construct(Orm $orm, ?User $user, Translator $translator)
    {
        $this->orm = $orm;
        $this->user = $user;
        $this->translator = $translator;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/UserForm.latte');
        $this->template->user = $this->user;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addText('email', 'Email')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('name', 'Jméno')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('surname', 'Příjmení')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('phoneNumber', 'Telefon')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $rolesArr = [];
        $roles = $this->orm->roles->findAll()->fetchPairs('id', 'intName');
        foreach ($roles as $id => $name)
        {
            $rolesArr[$id] = $this->translator->translate('roles.'.$name);
        }
        $form->addSelect('role', 'Role', $rolesArr)
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSubmit('send', $this->user ? 'Upravit' : 'Přidat')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onSuccess[] = [$this, 'onSuccess'];

        if ($this->user)
        {
            $defaults = $this->user->toArray();
            unset($defaults['role']);
            $defaults['role'] = $this->user->role->id;
            $form->setDefaults($defaults);
        }

        return $form;
    }

    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        if (!$this->user)
        {
            $user = new User();
        } else {
            $user = $this->user;
        }

        $user->name = $values->name;
        $user->surname = $values->surname;
        $user->email = $values->email;
        $user->phoneNumber = $values->phoneNumber;
        $user->active = true;
        $user->createdAt = new \DateTimeImmutable();
        $user->role = $this->orm->roles->getById($values->role);

        $this->orm->persistAndFlush($user);

        if ($this->user) {
            $this->getPresenter()->flashMessage('Uživatel byl upravený', FlashMessages::SUCCESS);
        } else {
            $this->getPresenter()->flashMessage('Uživatel byl přidaný', FlashMessages::SUCCESS);
        }

        $this->getPresenter()->redirect('edit', ['id' => $user->id]);
    }
}