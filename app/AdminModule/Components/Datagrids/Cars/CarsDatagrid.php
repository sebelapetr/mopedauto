<?php
declare(strict_types=1);

namespace App\AdminModule\Components\Datagrids;

use App\Model\Orm;
use App\Model\Product;
use App\Model\ProductImage;
use App\Model\Role;
use App\Model\Vehicle;
use Nette;
use Nette\Utils\Html;

class CarsDatagrid extends BasicDatagrid
{
    protected Orm $orm;

    public function __construct(Orm $orm, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);
        $this->orm = $orm;
    }

    public function setup(): void
    {
        $domain = "entity.car";

        $this->setDataSource($this->orm->vehicles->findBy(['deleted' => false]));

        $this->addColumnText('id', 'common.id')
            ->setSortable()
            ->setFilterText();

        $this->addColumnText('image', 'common.image')
            ->setRenderer(function(Vehicle $car) {
                $image = $car->getMainImage(ProductImage::SIZE_S);
                if (!$image) {
                    return '';
                }
                $html = Html::el('img', ['src' => $image->filePath, 'class' => 'products-datagrid-img']);
                return $html;
            })
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("name", $domain.".name")
            ->setSortable()
            ->setFilterText();

        $this->addColumnText("priceCzk", $domain.".priceCzk")
            ->setSortable()
            ->setFilterRange();

        $this->addColumnText("priceEur", $domain.".priceEur")
            ->setSortable()
            ->setFilterRange();

        $this->addAction('detail', 'Detail')
            ->setClass('btn btn-success btn-sm');

        $this->addAction('delete', 'common.delete', 'delete!')
            ->setClass('btn btn-sm btn-danger ajax');
    }
}