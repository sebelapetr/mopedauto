<?php

namespace App\Model;

use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;
use Tracy\Debugger;
use Nette\SmartObject;

class ProductService{

    private \App\Model\Orm $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function getCategories(){
        if ($this->categoriesExists()) {
            $categories = $this->orm->categories->findAll();
            foreach ($categories as $category) {
                $categorie[$category->id] = $category->name;
            }
        }
        else{
            $categorie[] = 'Nebyla nalezena žádná kategorie';
        }
        return $categorie;
    }

    public function productsExists() {
        $number = $this->orm->products->findAll()->countStored();
        $number = $number>0?true:false;
        return $number;
    }

    public function categoriesExists() {
        $number = $this->orm->categories->findAll()->countStored();
        $number = $number>0?true:false;
        return $number;
    }

    public function getProducts(){
        if ($this->productsExists()){
            $products = $this->orm->products->findAll()->orderBy('productName', 'ASC');
            foreach ($products as $product){
                $productsList[$product->id] = $product->productName;
            }
        }
        else{
            $productsList[] = 'Nebyl nalezen žádný produkt';
        }
        return $productsList;
    }

    public function getDefaultValues($id, $value){
        if($id) {
            $product = $this->orm->products->getById($id)->$value;
            if (isset($product)) {
                return $product;
            }
        }
    }

    public function editProduct(?Product $product, ArrayHash $values)
    {
        if (!$product) {
            $product = new Product();
        }
        $product->productName = $values->productName;
        $product->seoName = $values->seoName;
        $product->number = $values->number;
        $product->discount = $values->discount;
        $product->new = $values->new;
        $product->isHeavy = $values->isHeavy;
        $product->visible = $values->visible;
        $product->description = $values->description;
        $product->stockStatus = $values->stockStatus;
        $product->catalogPriceVat = $values->catalogPriceVat;
        $product->condition = $values->condition;
        $product->color = $values->color;
        $product->manufacturer = $values->manufacturer;
        $product->material = $values->material;
        $product->gtin = $values->gtin;
        $this->orm->persistAndFlush($product);
        return $product;
    }

    public function redirectTo($values){
        return $values->product;
    }
}