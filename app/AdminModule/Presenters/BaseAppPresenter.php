<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:31
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\IMenuComponentFactory;
use App\AdminModule\Components\MenuComponent;
use App\Model\Enum\FlashMessages;
use App\Model\Exceptions\PermissonsException;
use App\Model\Orm;
use App\Model\Setting;
use Contributte\Translation\LocalesResolvers\Session;
use App\Model\Enum\SettingsEnum;
use Nette\Localization\ITranslator;
use Tracy\Debugger;

abstract class BaseAppPresenter extends BasePermissonPresenter
{

    /** @inject */
    public ITranslator $translator;

    /** @inject */
    public Session $translatorSessionResolver;

    /** @inject */
    public IMenuComponentFactory $menuComponentFactory;

    /** @inject */
    public Orm $orm;

    public function startup()
    {
        parent::startup();
        if ($this->orm->settings->getBy(['option' => SettingsEnum::OPTION_MAINTENANCE_MODE])->getValueWithType() && !$this->isLinkCurrent('SystemSettings:default'))
        {
            echo
            "<div style='text-align: center;font-family: Arial'>
                <h1>Systém je nyní v režimu údržby</h1>
                <p>Zkuste stránku načíst později.</p>
             </div>
            "
            ;
            exit;
        }

        if (!$this->isLinkCurrent('Dashboard:default') && !$this->isLinkCurrent('Authentication:*')) {
            try {
                $isAllowed = $this->user->isAllowed(strtolower($this->getPureName()), 'read');
                if (!$isAllowed) {
                    $this->flashMessage('Do této sekce nemáte přístup.', FlashMessages::DANGER);
                    $this->redirect('Dashboard:');
                }
            } catch (PermissonsException $exception)
            {
                $this->flashMessage($exception->getMessage(), FlashMessages::DANGER);
                $this->redirect('Dashboard:default');
            }
        }

    }

    public function beforeRender()
    {
        parent::beforeRender();
        $prefixedTranslator = $this->translator->createPrefixedTranslator('app');
        $this->template->title = $prefixedTranslator->translate(strtolower($this->getPureName()).'.title');
    }

    public function createComponentMenuComponent(string $name): MenuComponent
    {
        return $this->menuComponentFactory->create();
    }

    public function handleToggleWork(): void
    {
        $this->user->user->workingNow = $this->user->user->workingNow ? false : true;
        $this->orm->persistAndFlush($this->user->user);
        $this->flashMessage($this->user->user->workingNow ? 'Byl/a jste přihlášen/a do práce.' : 'Byl/a jste odhlášen/a z práce.', FlashMessages::SUCCESS);
        $this->redirect('this');
    }
}
