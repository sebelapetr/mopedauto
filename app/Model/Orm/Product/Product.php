<?php

namespace App\Model;

use Nette\Application\LinkGenerator;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * Class Product
 * @package App\Model
 * @property int $id {primary}
 * @property string|NULL $productName
 * @property string|NULL $brandName
 * @property int|NULL $vendorInternalId
 * @property string|NULL $number
 * @property string|NULL $vendorName
 * @property string|NULL $ean
 * @property string|NULL $seoName
 * @property float|NULL $catalogPrice
 * @property float|NULL $catalogPriceVat
 * @property float|NULL $clientPrice
 * @property float|NULL $clientPriceVat
 * @property float|NULL $retailPrice
 * @property int|NULL $vat
 * @property bool|NULL $inStock
 * @property int|NULL $minStockLevel
 * @property int|NULL $stockLevel
 * @property string|NULL $image
 * @property string|NULL $description
 * @property int|NULL $weight
 * @property bool|NULL $recept
 * @property boolean $visible {default false}
 * @property string|NULL $sukl
 * @property string|NULL $pdkId
 * @property string|NULL $priceList
 * @property string|NULL $restrictions
 * @property string|NULL $tooOrder
 * @property ProductCategory[]|NULL|OneHasMany $productCategories {1:m ProductCategory::$product}
 * @property Quote|NULL $quote {1:m Quote::$product}
 * @property OrdersItem|NULL $orderItem {1:m OrdersItem::$product}
 * @property ProductImage[]|NULL|OneHasMany $images {1:m ProductImage::$product}
 * @property int|NULL $saled
 * @property string $url {virtual}
 * @property boolean $deleted {default false}
 * @property boolean $sale {default false}
 * @property boolean $isHeavy {default false}
 * @property boolean $new {default false}
 * @property boolean $discount {default false}
 */
class Product extends Entity
{

    public function getMainImage(string $size): ?ProductImage
    {
        return $this->images->countStored() !== 0 ? $this->images->toCollection()->getBy(['sort' => 0, 'size' => $size]) : null;
    }

    public function getMainCategory(): ?Category
    {
        return $this->productCategories->countStored() !== 0 ? $this->productCategories->toCollection()->getBy([])->category : null;
    }

    public function getterUrl(): string
    {
        return $this->seoName ? $this->id . "-" . $this->seoName : $this->id;
    }
}