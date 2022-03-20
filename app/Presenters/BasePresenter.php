<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:26
 */

declare(strict_types=1);

namespace App\Presenters;

use App\FrontModule\Forms\IFindFormFactory;
use App\FrontModule\Forms\INewsletterFormFactory;
use App\Model\Orm;
use App\Model\Session\CartService;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{

    /** @inject */
    public Orm $orm;

    /** @inject */
    public CartService $cartService;

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

    public function handleLogOut()
    {
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
        $this->template->productsInCartCount = $this->cartService->getOrder()->ordersItems->countStored();
    }

    protected function createComponentNewsletterForm()
    {
        return $this->newsletterFormFactory->create();
    }

    protected function createComponentFindForm()
    {
        return $this->findFormFactory->create();
    }

    protected function getPureName(): string
    {
        $pos = strrpos($this->name, ':');
        if (is_int($pos)) {
            return substr($this->name, $pos + 1);
        }
        return $this->name;
    }

    public function isLinkCurrentIn($links): bool
    {
        foreach(explode('|', $links) as $item) {
            if($this->isLinkCurrent($item)){
                return TRUE;
            }
        }
        return FALSE;
    }
}
