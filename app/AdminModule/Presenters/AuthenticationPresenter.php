<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:21
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;


use App\AdminModule\Forms\ILoginFormFactory;
use Nette\ComponentModel\IComponent;

class AuthenticationPresenter extends BaseAppPresenter
{
    /** @inject */
    public ILoginFormFactory $loginFormFactory;

    public const BASE_LOGGED_LINK = "Dashboard:default";
    public const BASE_UNLOGGED_LINK = "Authentication:default";

    public function createComponentLoginForm(string $name): ?IComponent
    {
        return $this->loginFormFactory->create();
    }
}