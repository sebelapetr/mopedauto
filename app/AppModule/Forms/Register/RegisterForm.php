<?php
declare(strict_types=1);

namespace App\AppModule\Forms;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use App\Model\UserRegistrationService;



class RegisterForm extends Control{

    /** @var UserRegistrationService  */
    public $registrationService;


    public function __construct(UserRegistrationService $registrationService)
    {
        //parent::__construct($registrationService);
        $this->registrationService = $registrationService;
    }

    protected function createComponentRegisterForm(){
        $form = new Form();
        $form->addEmail("email", "Email");
        $form->addPassword("password", "Password",NULL,"100");
        $form->addSubmit("submit");
        $form->onSuccess[] = [$this, 'registerFormSucceeded'];
        return $form;
    }

    public function registerFormSucceeded(Form $form, $values){
        try {
            $this->registrationService->registerUser($values['email'], $values['password']);
            $this->getPresenter()->flashMessage("Úspešně přihlášen");
            $this->getPresenter()->redirect("Dashboard:default");
        } catch(\Exception $exception) {
            $this->getPresenter()->flashMessage("Chybný email, nebo heslo");
        }
    }

    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/Register/Register.latte");
        $this->getTemplate()->render();
    }


}