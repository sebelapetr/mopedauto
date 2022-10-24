<?php
declare(strict_types=1);

namespace App\CustomerModule\Presenters;

abstract class BaseCustomerPresenter extends \App\FrontModule\Presenters\BasePresenter
{
	public function startup(): void {
	    if (!$this->getUser()->isLoggedIn())
        {
            $this->flashMessage('Pro přístup do zákaznické sekce musíte být přihlášen.', "error");
            $this->redirect(':Front:Homepage:default');
        }
		parent::startup();
    }
}
