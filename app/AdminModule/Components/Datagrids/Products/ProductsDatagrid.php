<?php
declare(strict_types=1);

namespace App\AdminModule\Components\Datagrids;

use App\Model\Orm;
use App\Model\Partner;
use App\Model\Product;
use App\Model\ProductImage;
use App\Model\Role;
use Nette;
use Nette\Utils\Html;

class ProductsDatagrid extends BasicDatagrid
{
    protected Orm $orm;

    public function __construct(Orm $orm, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);
        $this->orm = $orm;
    }

    public function setup(): void
    {
        $domain = "entity.product";

        $this->setDataSource($this->orm->products->findBy(['deleted' => false]));

        $this->addColumnText('id', 'common.id')
            ->setSortable()
            ->setFilterText();

        $this->addColumnText('image', 'common.image')
            ->setRenderer(function(Product $product) {
                $image = $product->getMainImage(ProductImage::SIZE_S);
                if (!$image) {
                    return '';
                }
                $html = Html::el('img', ['src' => $image->filePath, 'class' => 'products-datagrid-img']);
                return $html;
            })
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("productName", $domain.".name")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("number", $domain.".number")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("visible", $domain.".visible")
            ->setSortable()
            ->setRenderer(function(Product $product) {
                return $product->visible ? 'Ano' : 'Ne';
            })
            ->setFilterSelect([
                null => 'Vše',
                true => 'Ano',
                false => 'Ne'
            ]);

        $this->addColumnText("catalogPriceVat", $domain.".catalogPriceVat")
            ->setSortable()
            ->setFilterRange();

        $this->addAction('detail', 'Detail')
            ->setClass('btn btn-success btn-sm');

        $this->addAction('delete', 'common.delete', 'delete!')
            ->setClass('btn btn-sm btn-danger ajax');
    }
}