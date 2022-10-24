<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\IFindFormFactory;
use App\FrontModule\Forms\INewsletterFormFactory;
use App\Model\Session\CartService;
use Nette\Application\UI\Presenter;
use App\Model\Orm;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\DI\Container;
use Nextras\Dbal\Connection;
use Tracy\Debugger;

abstract class BasePresenter extends Presenter{

    /** @inject  */
    public Orm $orm;

    /** @inject */
    public CartService $cartService;

    /** @inject */
    public INewsletterFormFactory $newsletterFormFactory;

    /** @inject */
    public IFindFormFactory $findFormFactory;

    public Connection $connection;

    public Cache $cache;

    public Container $container;

    public const INITIAL_CATEGORY_SPARE_PARTS = "nahradni-dily";
    public const INITIAL_CATEGORY_CARS = "nabidka_mopedaut";

    public function __construct(Orm $orm, connection $connection, Container $container)
    {
        parent::__construct();
        $this->orm = $orm;
        $this->connection = $connection;
        $this->container = $container;
    }

    public function handleLogOut(){
        $this->getPresenter()->getUser()->logout();
        $this->flashMessage("Logout success");
        $this->getPresenter()->redirect(":Front:Homepage:default");
    }

    public function startup()
    {
        parent::startup();
        $storage = new FileStorage(TEMP_DIR);
        $this->cache = new Cache($storage, 'categories');
    }

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->productsInCartCount = $this->cartService->getOrder()->ordersItems->countStored();
    }

    protected function createComponentNewsletterForm(){
        return $this->newsletterFormFactory->create();
    }

    protected function createComponentFindForm(){
        return $this->findFormFactory->create();
    }

    public function handleRemoveProductFromCart($id)
    {
        $orderItem = $this->cartService->getOrder()->ordersItems->toCollection()->getById($id);
        $this->cartService->removeProductFromCart($orderItem);
        $this->redirect('this');
    }

    public function handleAddProductQuantity($id)
    {
        $orderItem = $this->cartService->getOrder()->ordersItems->toCollection()->getById($id);
        $this->cartService->addProductQuantity($orderItem);
        $this->redirect('this');
    }

    public function handleRemoveProductQuantity($id)
    {
        $orderItem = $this->cartService->getOrder()->ordersItems->toCollection()->getById($id);
        $this->cartService->removeProductQuantity($orderItem);
        $this->redirect('this');
    }
}