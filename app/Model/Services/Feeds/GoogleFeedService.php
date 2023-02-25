<?php

namespace App\Model\Services\Feeds;

use App\Model\Orm;
use App\Model\ProductImage;
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
        $feed = new Feed("mopedauto.cz", $_SERVER["SERVER_NAME"]."/feed/google.xml", "");

        foreach ($this->orm->products->findBy(["allowedGoogleFeed" => true, "deleted" => false, "description!=" => null]) as $product) {

            if (strlen($product->description) <= 10 || $product->getMainImage(ProductImage::SIZE_LG) == null) {
                continue;
            }
            $item = new Product();

            // Set common product properties
            $item->setId($product->id);
            $item->setTitle($product->productName);
            $item->setDescription($product->description ? $product->description : "");
            $item->setLink($_SERVER["SERVER_NAME"]."/".$product->url);
            $item->setImage($_SERVER["SERVER_NAME"].$product->getMainImage(ProductImage::SIZE_LG)->filePath);
            if ($product->stockStatus === \App\Model\Product::STOCK_STATUS_ONSTOCK) {
                $item->setAvailability(Availability::IN_STOCK);
            } elseif ($product->stockStatus === \App\Model\Product::STOCK_STATUS_TOWEEK || $product->stockStatus == \App\Model\Product::STOCK_STATUS_TOWEEK) {
                $item->setAvailability(Availability::BACKORDER);
            } else {
                $item->setAvailability(Availability::OUT_OF_STOCK);
            }
            $item->setPrice("{$product->catalogPriceVat} CZK");
            //$item->setGoogleCategory($product->getMainCategory()->categoryName); //TODO
            //$item->setBrand($product->brandName); //todo
            //$item->setGtin($product->ean); //todo
            $item->setCondition($product->condition === \App\Model\Product::CONDITION_NEW ? Product\Condition::NEW_PRODUCT : Product\Condition::USED);

            // Some additional properties
            //$item->setColor($product->color);
            //$item->setSize($product->size);

            // Shipping info
            /*
            $shipping = new Shipping();
            $shipping->setCountry('US');
            $shipping->setRegion('CA, NSW, 03');
            $shipping->setPostalCode('94043');
            $shipping->setLocationId('21137');
            $shipping->setService('UPS Express');
            $shipping->setPrice('1300 USD');
            $item->setShipping($shipping);

            // Set a custom shipping label and weight (optional)
            $item->setShippingLabel('ups-ground');
            $item->setShippingWeight('2.14');

            // Set a custom label (optional)
            $item->setCustomLabel('Some Label 1', 0);
            $item->setCustomLabel('Some Label 2', 1);
*/
            // Add this product to the feed
            $feed->addProduct($item);
        }

        return $feed;
    }
}