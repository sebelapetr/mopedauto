<?php

namespace App\CronModule\Model;

use Curl\Curl;
use App\Model\Category;
use App\Model\CategoryParent;
use App\Model\Product;
use Nette\Utils\Strings;
use Tracy\Debugger;
use App\Model\Orm;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;

class FeederService{

    /** @var Orm */
    private $orm;

    /** @var array */
    protected $api;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function downloadData()
    {
        $fileUrl = dirname(__FILE__) . '/data.xml';
        $fp = fopen ($fileUrl, 'w+');
        $api = new Curl();
        $api->setBasicAuthentication('16203/SEBELA', 'vichr');
        $api->setOpt(CURLOPT_FILE, $fp);
        $api->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $api->get('https://www.hs-online.cz:8081/restapi/b2b/zbozi');
        $api->close();
        if ($api->error) {
            echo 'ERROR - ';
            echo $api->error_code;
        }
        else {
            echo 'OK - ';
            echo $api->response;
        }
    }

    public function downloadStock()
    {
        $fileUrl = dirname(__FILE__) . '/sklad.xml';
        $fp = fopen ($fileUrl, 'w+');
        $api = new Curl();
        $api->setBasicAuthentication('16203/SEBELA', 'vichr');
        $api->setOpt(CURLOPT_FILE, $fp);
        $api->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $api->get('https://www.hs-online.cz:8081/restapi/b2b/zbozi/stav');
        $api->close();
        if ($api->error) {
            echo 'ERROR - ';
            echo $api->error_code;
        }
        else {
            echo 'OK - ';
            echo $api->response;
        }
    }

    public function downloadData1(){
        /*
        $api = new Curl();
        $api->setBasicAuthentication('16203/SEBELA', 'vichr');
        //$api->get('https://www.hs-online.cz:8081/restapi/b2b/zbozi');
        $api->get('https://www.hs-online.cz:8081/restapi/b2b/zbozi/stav?id=48746');
        if ($api->error) {
            echo $api->error_code;
        }
        else {
            echo $api->response;
        }
        */
    }

    /* FEED ONE HOUR */

    public function feedStock(){
        $file = (__DIR__ . '/sklad.xml');
        $xml=simplexml_load_file($file) or die("Error: Cannot create object");
        foreach ($xml->productStav as $item){
            $product = $this->orm->products->getBy(['id'=>$item->productId]);
            if($product) {
                try {
                    if (intval($item->minStockLevel)>1){
                        $product->minStockLevel = intval($item->minStockLevel);
                        $product->inStock = true;
                        if (intval($item->minStockLevel) < 3) {
                            $product->stockLevel = 0;
                        } elseif (intval($item->minStockLevel) < 10) {
                            $product->stockLevel = 3;
                        } else {
                            $product->stockLevel = 10;
                        }
                    } else {
                        $product->inStock = false;
                    }
                    $this->orm->persist($product);
                } catch (\Exception $e) {
                    Debugger::barDump($e);
                }
            }
        }
        $this->orm->flush();
    }

    /* /FEED ONE HOUR */


    /* FEED ONE DAY */

    /* /FEED ONE DAY */

    /* INIT */

    public function initImagesAndDescription(){
        //https://www.hs-online.cz:8081/restapi/b2b/zbozi/id/foto
        $file = (__DIR__ . '/data.xml');
        $xml=simplexml_load_file($file) or die("Error: Cannot create object");
        foreach ($xml->product as $item) {
            //PREMENA NA URL
            $productName = Strings::webalize($item->productName, '-');
            $product = $this->orm->products->getById(intval($item->productInternalId));
            if ($product) {
                try {
                    if (!$product->image) {
                        $product->image = $productName . '.png';

                        //------------------------------------------------//

                        $api = new Curl();
                        $api->setBasicAuthentication('16203/SEBELA', 'vichr');
                        $api->get('https://www.hs-online.cz:8081/restapi/b2b/zbozi/' . $product->id . '/foto');
                        if ($api->error) {
                            echo $api->error_code;
                        } else {
                            $xml2 = (simplexml_load_string($api->response));
                            /*
                            $file2 = (__DIR__ . '/foto.xml');
                            $xml2=simplexml_load_file($file2) or die("Error: Cannot create object");
                            */
                            $product->description = $xml2->info;
                            //---------------------------------------------------------//
                            if (!file_exists(__DIR__ . '/../../../www/images/products/' . $productName . '.png')) {
                                $data = base64_decode($xml2->foto);
                                $file = __DIR__ . '/../../../www/images/products/' . $productName . '.png';
                                $success = file_put_contents($file, $data);
                            }
                        }


                        $this->orm->persistAndFlush($product);
                    }
                } catch (\Exception $e) {
                    Debugger::barDump($e);
                }
            }
            else{
                Debugger::enable(Debugger::DETECT, __DIR__ . '/../missingProducts');
                Debugger::log($item->productInternalId);
            }
        }
    }

    public  function  initCats(){
        //api
        $file = (__DIR__ . '/data.xml');
        $xml=simplexml_load_file($file) or die("Error: Cannot create object");
        foreach ($xml->product as $item) {
            $categoryString = $item->categoryLabel;
            $categoryString = str_replace('neurčeno/', '', $categoryString);
            $categoryString = str_replace('neurčeno', '', $categoryString);
            $categoryString = rtrim($categoryString, '/');
            $categoryArray = explode('/',$categoryString);
            $categoryArrayCount = count($categoryArray);
            for ($a=1;$a<=$categoryArrayCount;$a++){
                $string = '';
                for($i=0;$i<$a;$i++){
                    $string = $string.$categoryArray[$i].'/';
                }
                $string = rtrim($string,'/');
                $categoryName = $string;
                $categoryLabel = Strings::webalize($string,'/');
                $parentLabel = $categoryName;
                //DB-------------------------
                $categoryExist = $this->orm->categories->getBy(['categoryLabel'=>$categoryLabel]);
                if(!$categoryExist) {
                    $parentLabelArray = explode('/',$parentLabel);
                    $categorySingleName = array_pop($parentLabelArray);
                    $parentLabel = implode($parentLabelArray, '/');
                    $categoryParent = $this->orm->categories->getBy(['categoryLabel'=>Strings::webalize($parentLabel,'/')]);

                    $category = new Category();
                    $category->categoryName = $string;
                    $category->categoryLabel = $categoryLabel;
                    $category->categorySingleName = $categorySingleName;
                    //$this->orm->persist($category);
                    $this->orm->persistAndFlush($category);

                    $parentCategory = new CategoryParent();
                    $parentCategory->category = $category;
                    $parentCategory->parent = $categoryParent;
                    //$this->orm->persist($parentCategory);
                    $this->orm->persistAndFlush($parentCategory);

                }
                //---------------------------
            }
        }
    }
    /* /INIT */



    public function feedOneHourCategories(){

        $file = (__DIR__ . '/data.xml');
        $xml=simplexml_load_file($file) or die("Error: Cannot create object");

        foreach ($xml->product as $item) {
            //TRANSFORMACE JMENA
            $categoryName = str_replace('neurčeno/', '', $item->categoryLabel);
            $categoryName = str_replace('neurčeno', '', $categoryName);
            $categoryName = rtrim($categoryName, '/');
            //PREMENA NA URL
            $categoryLabel = Strings::webalize($categoryName, '/');

            $category = $this->orm->categories->getBy(['categoryLabel'=>$categoryLabel]);
            if(!$category)
            {
                $category = new Category();
                $category->categoryName = $categoryName;
                $category->categoryLabel = Strings::webalize($categoryLabel, '/');
                $this->orm->persistAndFlush($category);
            }
        }
    }
    public function feedOneHourParents(){
        /*
        $string = 'Řita ora/ašč/šč';
        $array = explode('/',$string);
        if (count($array)>1){
            $pop=array_pop($array);
        }
        $string = implode('/',$array);
        $result = Strings::webalize($string, '/');
        print_r($result);
        */
        $file = (__DIR__ . '/data.xml');
        $xml=simplexml_load_file($file) or die("Error: Cannot create object");
        foreach($xml->product as $item){

            $categoryName = str_replace('neurčeno/', '', $item->categoryLabel);
            $categoryName = str_replace('neurčeno', '', $categoryName);
            $categoryName = rtrim($categoryName, '/');
            $categoryLabel = Strings::webalize($categoryName, '/');

            $parentName = explode('/', $categoryName);
            if(count($parentName)>1) {
                array_pop($parentName);
            }
            $parentName = implode('/', $parentName);
            $parent = Strings::webalize($parentName, '/');

            $category = $this->orm->categories->getBy(['categoryLabel'=>$categoryLabel]);
            $parentExist = $this->orm->categories->getBy(['categoryLabel'=>$parent]);

            if(!$parentExist){
                $newCategory = new Category();
                $newCategory->categoryName = $parentName;
                $newCategory->categoryLabel = $parent;
                $this->orm->persistAndFlush($newCategory);
                $parentExistId = $newCategory->id;
            }
            else{
                $parentExistId = $parentExist->id;
            }

            $categoryParent = $this->orm->categoryParents->getBy(['category'=>$category->id,'parent'=>$parentExistId]);

            if (!$categoryParent){
                $parentCategory = new CategoryParent();
                $parentCategory->category = $category;
                $parentCategory->parent = $parentExist;
                $this->orm->persist($parentCategory);
            }
            else{
                /*
                $parentCategory = new CategoryParent();
                $parentCategory->category = $category;
                $parentCategory->parent = ;
                $this->orm->persist($parentCategory);
                */
            }
        }
        $this->orm->flush();
    }

    public function feedOneHourProducts(){
        $file = (__DIR__ . '/data.xml');
        $xml=simplexml_load_file($file) or die("Error: Cannot create object");
        foreach ($xml->product as $item){
            //TRANSFORMACE JMENA
            $categoryName = str_replace('neurčeno/', '', $item->categoryLabel);
            $categoryName = str_replace('neurčeno', '', $categoryName);
            $categoryName = rtrim($categoryName, '/');
            //PREMENA NA URL
            $categoryLabel = Strings::webalize($categoryName, '/');

            $category = $this->orm->categories->getBy(['categoryLabel'=>$categoryLabel]);
            if(!$category) $category->id=null;
            $product = new Product();
            $product->id = intval($item->productInternalId);
            $product->productName = htmlentities(str_replace(['%'], ['&#37;'], $item->productName));
            $product->brandName = htmlentities($item->brandName);
            $product->vendorInternalId = intval($item->vendorInternalId);
            $product->vendorName = htmlentities($item->vendorName);
            $product->ean = $item->ean;
            $product->catalogPrice = floatval($item->catalogPrice);
            $product->catalogPriceVat = floatval($item->catalogPriceVAT);
            $product->clientPrice = floatval($item->clientPrice);
            $product->clientPriceVat = floatval($item->clientPriceVAT);
            $product->retailPrice = floatval($item->retailPrice);
            $product->vat = intval($item->VAT);
            $product->inStock = $item->inStock == 'true' ? 1 : 0;
            $product->minStockLevel = intval($item->minStockLevel);
            $product->stockLevel = intval($item->stockLevel);
            $product->weight = intval($item->weight);
            $product->recept = $item->recept == 'true' ? 1 : 0;
            $product->active = 1;
            $product->sukl = $item->sukl;
            $product->pdkId = $item->pdkID;
            $product->url = $item->url;
            $product->priceList = htmlentities($item->priceList);
            $product->restrictions = $item->restrictions;
            $product->tooOrder = $item->tooOrder;
            $product->category = $category;
            try {
                $this->orm->products->insertProduct($product);
            } catch (\Exception $e) {
                Debugger::enable(Debugger::DETECT, __DIR__ . '/../feedLog');
                Debugger::log($e);
            }
        }

    }
}