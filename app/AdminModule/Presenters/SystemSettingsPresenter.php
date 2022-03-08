<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:24
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\MenuComponent;
use App\AdminModule\Forms\ISettingsFormFactory;
use App\AdminModule\Forms\SettingsForm;

class SystemSettingsPresenter extends BaseAppPresenter
{
    /** @inject */
    public ISettingsFormFactory $settingsFormFactory;

    public function createComponentSettingsForm(string $name): SettingsForm
    {
        return $this->settingsFormFactory->create();
    }
}