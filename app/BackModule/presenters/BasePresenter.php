<?php

namespace App\BackModule\Presenters;

use App\FrontModule\Forms\INewsletterFormFactory;
use Nette\Application\UI\Presenter;
use App\Model\Orm;

class BasePresenter extends Presenter{

    /** @var Orm  */
    protected $orm;

    public function __construct(Orm $orm)
    {
        parent::__construct();
        $this->orm = $orm;
    }

    public function handleLogOut(){
        $this->getPresenter()->getUser()->logout();
        $this->flashMessage("Úspěšně odhlášen");
        $this->getPresenter()->redirect(":Back:Dashboard:default");
    }

    public function startup(){
        parent::startup();
        if(!$this->getUser()->isLoggedIn()){
            if(!($this->isLinkCurrent('Login:') || $this->isLinkCurrent('Register:'))){
                $this->redirect('Login:');
            }
        }else{
            if($this->isLinkCurrent('Login:') || $this->isLinkCurrent('Register:')){
                $this->redirect('Dashboard:');
            }
        }
        $this->getPresenter()->getTemplate()->userEmail = $this->getUserEmail();
    }

    public function getUserEmail(){
        if($this->getUser()->isLoggedIn()) {
            $id = $this->getPresenter()->getUser()->id;
            $user = $this->orm->users->getById($id);
            return $user->email;
        }
        else{
            return 'Nepřihlášen';
        }
    }
    /*
    public function startup()
    {
        if (!$this->getUser()->isLoggedIn()){
            if($this->isLinkCurrent("Register:")){
                $this->redirect("LogIn:");
            }
            else{
                if(!$this->isLinkCurrent("LogIn:")){
                    $this->redirect("LogIn:");
                }
            }

        }
        else{
            if ($this->getUser()->isLoggedIn()){
                if($this->isLinkCurrent("LogIn:")){
                    $this->redirect("HomePage:");
                }
            }
        }
    })*/
}