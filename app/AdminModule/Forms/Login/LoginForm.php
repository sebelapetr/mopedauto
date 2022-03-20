<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\AdminModule\Presenters\AuthenticationPresenter;
use App\Model\City;
use App\Model\Document;
use App\Model\Orm;
use Nette;
use Tracy\Debugger;
use function Symfony\Component\String\b;

class LoginForm extends Nette\Application\UI\Control
{
    private Orm $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/LoginForm.latte');
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addText('email', 'Email')
            ->setHtmlAttribute('class', 'form-control form-control-user');

        $form->addPassword('password', 'Heslo')
            ->setHtmlAttribute('class', 'form-control form-control-user');

        $form->addSubmit('login', 'Přihlásit se')
            ->setHtmlAttribute('class', 'btn btn-primary btn-user btn-block');

        $form->onSuccess[] = [$this, 'onSuccess'];
        $form->onValidate[] = [$this, 'onValidate'];

        return $form;
    }

    public function onValidate(Nette\Application\UI\Form $form): void
    {

    }

    public function onSuccess(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values): void
    {
        try {
            $this->getPresenter()->getUser()->login($values->email, $values->password);
        } catch (\Exception $exception) {
            $form->addError($exception->getMessage());
            return;
        }

        $this->getPresenter()->flashMessage('common.login.success', 'SUCCESS');
        $this->getPresenter()->redirect(AuthenticationPresenter::BASE_LOGGED_LINK);
    }
}