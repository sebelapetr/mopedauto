<?php

namespace App\BackModule\Presenters;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use App\BackModule\Forms\IRegisterFormFactory;
use App\Model\Orm;

class RegisterPresenter extends BasePresenter{

    /**
     * @var IRegisterFormFactory
     */
    public $registerFormFactory;

    public function __construct(Orm $orm, IRegisterFormFactory $registerFormFactory)
    {
        parent::__construct($orm);
        $this->registerFormFactory = $registerFormFactory;
    }

    public function renderDefault(){
        $this->getTemplate()->setFile(__DIR__  .  "/../templates/Register/default.latte");
    }


    protected function createComponentRegisterForm(){
        return $this->registerFormFactory->create();
    }
}
