<?php

namespace App\Model;

use Nette\Application\LinkGenerator;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\ManyHasMany;
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
 * @property ManyHasMany|ProductParameterValue $parameterValues {m:m ProductParameterValue::$products, isMain=TRUE}
 * @property string $condition {enum self::CONDITION_*} {default self::CONDITION_USED}
 * @property string $stockStatus {enum self::STOCK_STATUS_*} {default self::STOCK_STATUS_WEEK}
 * @property bool $allowedGoogleFeed {default false}
 */
class Product extends Entity
{

    public const CONDITION_NEW = "NEW";
    public const CONDITION_USED = "USED";

    public const STOCK_STATUS_ONSTOCK = "ONSTOCK";
    public const STOCK_STATUS_TOWEEK = "TOWEEK";
    public const STOCK_STATUS_WEEK = "WEEK";

    public function getMainImage(string $size): ?ProductImage
    {
        return $this->images->countStored() !== 0 ? $this->images->toCollection()->getBy(['sort' => 0, 'size' => $size]) : null;
    }

    public function getOtherImages(string $size): ICollection
    {
        return $this->images->toCollection()->findBy(['size' => $size])->limitBy(50, 1);
    }

    public function getImageOtherSize(ProductImage $productImage, string $size): ?ProductImage
    {
        $fileName = str_replace($productImage->size, $size, $productImage->fileName);
        return $this->images->toCollection()->getBy(['fileName' => $fileName]);
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