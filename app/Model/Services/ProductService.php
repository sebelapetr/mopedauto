<?php

namespace App\Model;

use Nette\Security\Passwords;
use Nette\Utils\FileSystem;
use Tracy\Debugger;
use Nette\SmartObject;

class ProductService{

    /** @var Orm */
    private $orm;

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

    public function addProduct($values){
        $product = $this->orm->products->getBy(['productName'=>$values->name]);
        if ($product !== NULL){
            ?>
            <script>alert("Pozor! produkt se stejným názvem již existuje.")</script>
            <?php
        }
        $product = new Product();
        $product->productName = $values->name;
        $product->normPrice = $values->normPrice;
        $product->price = $values->price;
        $product->manufacturer = $values->manufacturer;
        $product->deliveryTime = $values->deliveryTime;
        $product->length = $values->length;
        $product->width = $values->width;
        $product->height = $values->height;
        $product->weight = $values->weight;
        $product->dumbellsWeight = $values->dumbellsWeight;
        $product->description = $values->description;
        $product->image = $values->image->name;
        $product->visible = $values->visible;
        $product->new = $values->new;
        $product->repaired = $values->repaired;
        $this->orm->persistAndFlush($product);

        if ($this->categoriesExists()) {
            $product = $this->orm->products->getBy(["id" => $product->id]);
            $product->category = $values->category;
            $this->orm->persistAndFlush($product);
        }
    }

    public function editProduct($values){
        $product = $this->orm->products->getBy(["id"=>$values->id]);
        $product->name = $values->name;
        $product->normPrice = $values->normPrice;
        $product->price = $values->price;
        $product->manufacturer = $values->manufacturer;
        $product->deliveryTime = $values->deliveryTime;
        $product->weight = $values->weight;
        $product->dumbellsWeight = $values->dumbellsWeight;
        $product->length = $values->length;$product->width = $values->width;
        $product->height = $values->height;
        if ($values->image->name) {$product->image = $values->image->name;}
        $product->description = $values->description;
        $product->new = $values->new;
        $product->repaired = $values->repaired;
        $product->visible = $values->visible;
        $product->category = $values->category;
        $this->orm->persistAndFlush($product);
    }

    public function redirectTo($values){
        return $values->product;
    }
}