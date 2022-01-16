<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\IFindFormFactory;
use App\FrontModule\Forms\INewsletterFormFactory;
use Nette\Application\UI\Presenter;
use App\Model\Orm;

abstract class BasePresenter extends Presenter{

    /** @inject  */
    public Orm $orm;

    /**
     * @var INewsletterFormFactory @inject
     */
    public $newsletterFormFactory;

    /** @inject */
    public IFindFormFactory $findFormFactory;

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

    public function beforeRender()
    {
        parent::beforeRender(); // TODO: Change the autogenerated stub
        $this->template->veterinaryParents = $this->getVeterinaryParents();
        $this->template->breedingParents = $this->getBreedingParents();
    }

    public function getVeterinaryParents(){
        $parent = $this->orm->categories->getById(1);
        return $this->orm->categoryParents->findBy(['parent'=>$parent])->orderBy('priority', 'ASC');
    }

    public function getBreedingParents(){
        return $this->orm->categoryParents->findBy(['parent'=>2])->orderBy('priority', 'ASC');
    }


    protected function createComponentNewsletterForm(){
        return $this->newsletterFormFactory->create();
    }

    protected function createComponentFindForm(){
        return $this->findFormFactory->create();
    }
}