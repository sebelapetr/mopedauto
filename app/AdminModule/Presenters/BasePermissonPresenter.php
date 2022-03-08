<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:31
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model\User;
use App\Presenters\BasePresenter;
use Tracy\Debugger;

abstract class BasePermissonPresenter extends BasePresenter
{
	public function startup()
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn() && !$this->isLinkCurrent(AuthenticationPresenter::BASE_UNLOGGED_LINK))
        {
            $this->redirect(AuthenticationPresenter::BASE_UNLOGGED_LINK);
        }
    }


    public function handleLogout(): void
    {
        $this->getUser()->logout();
        $this->redirect(AuthenticationPresenter::BASE_UNLOGGED_LINK);
    }
}
