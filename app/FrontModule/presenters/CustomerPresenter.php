<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\ILoginEmailFormFactory;
use App\FrontModule\Forms\ILoginPasswordFormFactory;
use App\FrontModule\Forms\IRegistrationFormFactory;
use App\FrontModule\Forms\LoginEmailForm;
use App\FrontModule\Forms\LoginPasswordForm;
use App\FrontModule\Forms\RegistrationForm;
use App\Model\Customer;
use App\Model\Orm;
use App\Model\Security\CustomerAuthenticator;
use Contributte\Translation\Translator;
use Nette\Application\UI\Form;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\Utils\ArrayHash;
use Nextras\Dbal\Connection;
use Tracy\Debugger;

class CustomerPresenter extends BasePresenter
{
    public bool $loginFormEmail = false;

    private ?string $loginEmail = "";

    /** @inject */
    public ILoginEmailFormFactory $loginEmailFormFactory;

    /** @inject */
    public ILoginPasswordFormFactory $loginPasswordFormFactory;

    /** @inject */
    public IRegistrationFormFactory $registrationFormFactory;

    public CustomerAuthenticator $customerAuthenticator;

    public Translator $translator;

    public function __construct(Orm $orm, Connection $connection, Container $container, Translator $translator)
    {
        $this->translator = $translator;
        parent::__construct($orm, $connection, $container);
    }

    public function startup()
    {
        parent::startup();
        /** @var CustomerAuthenticator customerAuthenticator */
        $customerAuthenticator = $this->container->getByName("customerAuthenticator");
        $this->customerAuthenticator = $customerAuthenticator;
    }

    public function actionLogin(): void
    {
        $this->getTemplate()->loginFormEmail = $this->loginFormEmail;
    }

    public function actionForgottenPassword(): void
    {

    }

    public function actionResetPassword(): void
    {

    }

    public function actionRegistration(): void
    {

    }

    public function actionConfirmEmail(): void
    {

    }

    public function createComponentLoginEmailForm(): LoginEmailForm
    {
        $form = $this->loginEmailFormFactory->create();
        $form->onSuccessCallback[] = [$this, 'loginEmailFormSuccess'];
        return $form;
    }

    public function loginEmailFormSuccess(Form $form, ArrayHash $values): void
    {
        $values = $form->getValues();
        /** @var Customer|null $customer */
        $customer = $this->orm->customers->getByEmail($values->email);

        if ($customer !== null) {
            if ($this->checkUser($customer)) {
                $this->getTemplate()->loginFormEmail = true;
                $this->loginEmail = $customer->email;
                $this->flashMessage('Uživatel byl nalezen. Zadejte heslo k účtu.', "success");
                $this->redrawControl('login');
                $this->redrawControl('flashes');
            } else {
                $this->redrawControl('flashes');
            }
        } else {
            $this->flashMessage('Uživatel se zadaným emailem nebyl nalezen. Chcete vytvořit účet?', "error");
            $this->redrawControl('flashes');
        }
    }

    private function checkUser(Customer $customer): bool
    {
        if ($customer->deleted || !$customer->active) {
            $this->flashMessage('Uživatel se zadaným emailem je smazaný nebo zablokovaný.', "error");
            return false;
        } else {
            if ($customer->confirmed) {
                return true;
            } else {
                $this->flashMessage('Uživatel se zadaným emailem nebyl potvrzený odkazem v potvrzovacím emailu.', "error");
                return false;
            }
        }
    }


    public function createComponentLoginPasswordForm(): ?LoginPasswordForm
    {
        $form = $this->loginPasswordFormFactory->create($this->loginEmail);
        $form->onSuccessCallback[] = [$this, 'loginPasswordFormSuccess'];
        return $form;
    }

    public function loginPasswordFormSuccess(Form $form)
    {
        $values = $form->getValues();
        $user = $this->orm->customers->getBy(['email' => $values->email]);
        if ($user) {
            if ($this->checkUser($user)) {
                try {
                    $this->getUser()->setAuthenticator($this->customerAuthenticator);
                    $this->getUser()->login($values->email, $values->password);
                } catch (\Exception $exception) {
                    $this->flashMessage($exception->getMessage(), "error");
                    $this->redrawControl('flashes');
                }
            }
        } else {
            $this->flashMessage('Uživatel nebyl nalezen.', "error");
            $this->redrawControl('flashes');
        }

        if ($this->getUser()->isLoggedIn()) {
            $this->flashMessage("Úspěšně jste se přihlásil.", "success");
            $this->redirect(':Customer:Dashboard:default');
        }
    }

    public function createComponentRegistrationForm(): RegistrationForm
    {
        return $this->registrationFormFactory->create();
    }

    public function handleLogout(): void
    {
        $this->user->logout(TRUE);
        $this->flashMessage("Úspěšně jste se odhlásil.", "success");
        $this->redirect(':Front:Homepage:default');
    }
}