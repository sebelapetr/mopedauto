<?php

namespace App\Model\Services\Feeds;

use App\Model\Orm;
use App\Model\ProductImage;
use Nette\Application\LinkGenerator;
use Nette\Security\Passwords;
use Tracy\Debugger;
use Vitalybaev\GoogleMerchant\Feed;
use Vitalybaev\GoogleMerchant\Product;
use Vitalybaev\GoogleMerchant\Product\Shipping;
use Vitalybaev\GoogleMerchant\Product\Availability\Availability;

class GoogleFeedService
{

    /** @var Orm */
    private $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function generate(): Feed
    {
        // Create feed object
        $feed = new Feed("mopedauto.cz", $_SERVER['HTTP_HOST']."/feed/google.xml", "");

        foreach ($this->orm->products->findBy(["allowedGoogleFeed" => true, "deleted" => false, "description!=" => null]) as $product) {

            if (strlen($product->description) <= 10 || $product->getMainImage(ProductImage::SIZE_LG) == null) {
                continue;
            }
            $item = new Product();

            // Set common product properties
            $item->setId($product->id);
            $item->setTitle($product->productName);
            $item->setDescription($product->description ? $product->description : "");
            $item->setLink($_SERVER['HTTP_HOST']."/nahradni-dily/produkt/".$product->url);
            $item->setImage($_SERVER['HTTP_HOST'].$product->getMainImage(ProductImage::SIZE_LG)->filePath);
            if ($product->stockStatus === \App\Model\Product::STOCK_STATUS_ONSTOCK) {
                $item->setAvailability(Availability::IN_STOCK);
            } elseif ($product->stockStatus === \App\Model\Product::STOCK_STATUS_TOWEEK) {
                $item->setAvailability(Availability::BACKORDER);
                $availabilityDate = new \DateTimeImmutable("+7 days");
                $item->setAttribute("availability_date", $availabilityDate->format(\DateTimeImmutable::ATOM));
            } else {
                $item->setAvailability(Availability::OUT_OF_STOCK);
            }
            $item->setPrice("{$product->catalogPriceVat} CZK");

            $item->setBrand($product->manufacturer);

            if ($product->color) {
                $item->setColor($product->color);
            }

            if ($product->gtin) {
                $item->setGtin($product->gtin);
            }

            if ($product->material) {
                $item->setMaterial($product->material);
            }
            //$item->setGoogleCategory($product->getMainCategory()->categoryName); //TODO

            $item->setCondition($product->condition === \App\Model\Product::CONDITION_NEW ? Product\Condition::NEW_PRODUCT : Product\Condition::USED);

            // Shipping info
            $shipping = new Shipping();
            $shipping->setCountry('CZ');

            if ($product->isHeavy) {
                $shipping->setService('TOPTRANS velký balík');
                $shipping->setPrice('249 CZK');
            } else {
                $shipping->setService('TOPTRANS malý balík');
                $shipping->setPrice('179 CZK');
            }

            $item->setShipping($shipping);


            // Add this product to the feed
            $feed->addProduct($item);
        }

        return $feed;
    }
}