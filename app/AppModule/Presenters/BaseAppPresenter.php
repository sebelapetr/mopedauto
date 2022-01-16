<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:31
 */

declare(strict_types=1);

namespace App\AppModule\Presenters;

use App\AppModule\Components\IMenuComponentFactory;
use App\AppModule\Components\MenuComponent;
use App\Model\Orm;
use Contributte\Translation\LocalesResolvers\Session;
use Nette\Localization\ITranslator;

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
}
