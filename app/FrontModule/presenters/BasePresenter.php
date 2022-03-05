<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\IFindFormFactory;
use App\FrontModule\Forms\INewsletterFormFactory;
use App\Model\Category;
use App\Model\Session\CartSession;
use Nette\Application\UI\Presenter;
use App\Model\Orm;
use Tracy\Debugger;

abstract class BasePresenter extends Presenter{

    /** @inject  */
    public Orm $orm;

    /** @inject */
    public CartSession $cartSession;

    /** @inject */
    public INewsletterFormFactory $newsletterFormFactory;

    /** @inject */
    public IFindFormFactory $findFormFactory;

    public const INITIAL_CATEGORY_SPARE_PARTS = "nahradni-dily";
    public const INITIAL_CATEGORY_CARS = "nabidka_mopedaut";

    public function __construct(Orm $orm)
    {
        parent::__construct();
        $this->orm = $orm;
    }

    public function handleLogOut(){
        $this->getPresenter()->getUser()->logout();
        $this->flashMessage("Logout success");
        $this->getPresenter()->redirect(":Front:Homepage:default");
    }

    public function startup()
    {
        parent::startup();
    }

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->productsInCartCount = $this->cartSession->getProducts() ? count($this->cartSession->getProducts()) : 0;
    }

    protected function createComponentNewsletterForm(){
        return $this->newsletterFormFactory->create();
    }

    protected function createComponentFindForm(){
        return $this->findFormFactory->create();
    }
}