<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\IAddProductFormFactory;
use App\Model\Product;
use App\Model\Session\CartSession;
use Nette\Utils\DateTime;
use Nette\Utils\Paginator;
use Tracy\Debugger;

class SparePartsPresenter extends BasePresenter
{
    /** @var int */
    public $limit;

    /** @var array  */
    public $pages = [];

    /** @var int */
    public $pagesNumber;

    /** @var  int */
    public $productsNumber;

    /** @var int */
    public $lastPage;

    public Product $currentProduct;

    /** @inject */
    public IAddProductFormFactory $addProductFormFactory;


    public function renderDefault($page=1)
    {
        $this->limit = 12;
        $offset = $page>0?($page-1)*$this->limit:$page;
        $this->getTemplate()->products = $this->orm->products->findAll()->limitBy($this->limit,$offset);
        $this->getTemplate()->pages = $this->getPages($page);
        $this->getTemplate()->actualPage = $page;
        $this->getTemplate()->pagesNumber = $this->pagesNumber;
        $this->getTemplate()->productsNumber = $this->productsNumber;
        $this->lastPage = ceil($this->orm->products->findAll()->countStored()/$this->limit);
        $this->getTemplate()->lastPage = $this->lastPage;
    }

    public function getPages($actualPage)
    {
        $count = $this->orm->products->findAll()->countStored();
        $paginator = new Paginator();
        $paginator->setItemCount($count);
        $paginator->setItemsPerPage($this->limit);
        $paginator->setPage($actualPage);

        if(ceil($count/$this->limit)>5) {
            if($paginator->isLast()) $actualPage--;
            $rozdil = 4 - $actualPage;
            $actualPage <= 5 ? $actualPage = $actualPage + $rozdil + 1 : '';
            for ($i = ($actualPage - 4); $i <= ($actualPage + 1); $i++) {
                $this->pages[] = $i;
            }
        }
        else{
            for ($i = ($actualPage-($actualPage-1)); $i <= (ceil($count/$this->limit)); $i++) {
                $this->pages[] = $i;
            }
        }
        return $this->pages;
    }

    public function actionDetail($id)
    {
        $this->currentProduct = $this->orm->products->getById($id);
    }

    public function renderDetail($id)
    {
        $this->getTemplate()->product = $this->currentProduct;
        $this->getTemplate()->productInCart = $this->productInCart($id);
        $this->getTemplate()->actualId = $id;
        $this->getTemplate()->session = $this->getSession()->getSection('products');
    }

    public function productInCart($id){
        if ($this->cartSession->getProducts()) {
            return array_key_exists($id, $this->cartSession->getProducts());
        }
        return false;
    }

    function cesky_den($den) {
        static $nazvy = array('neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota');
        return $nazvy[$den];
    }

    protected function createComponentAddProductForm(){
        return $this->addProductFormFactory->create($this->currentProduct);
    }
}