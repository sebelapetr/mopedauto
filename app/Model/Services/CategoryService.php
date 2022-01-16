<?php

namespace App\Model;

use Tracy\Debugger;
use Nette\Http\Url;

class CategoryService{

    /** @var Orm */
    private $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function getDefaultValues($id, $value){
        if($id) {
            $category = $this->orm->categories->getById($id)->$value;
            if (isset($category)) {
                return $category;
            }
        }
    }

    public function getCategories(){
        if ($this->categoriesExists()){
            $categories = $this->orm->categories->findAll()->orderBy('name', 'ASC');
            foreach ($categories as $category){
                $categorie[$category->id] = $category->name;
            }
        }
        else{
            $categorie[] = 'Nebyla nalezena žádná kategorie';
        }
        return $categorie;
    }

    public function categoriesExists() {
        $number = $this->orm->categories->findAll()->countStored();
        $number = $number>0?true:false;
        return $number;
    }

    public function editCategory($values){
        $category = $this->orm->categories->getBy(["id"=>$values->id]);
        $category->name = $values->name;
        $category->description = $values->description;
        $category->visible = $values->visible;
        $this->orm->persistAndFlush($category);
    }

    public function addCategory($values){
        $category = $this->orm->categories->getBy(["name"=>$values->name]);
        if ($category !== NULL){
            ?>
            <script>alert("Pozor! produkt se stejným názvem již existuje.")</script>
            <?php
        }


        $category = new Category();
        $category->name = $values->name;
        $category->description = $values->description;
        $category->visible = $values->visible;
        $this->orm->persistAndFlush($category);

        $parentCategory = new CategoryParent();
        $parentCategory->category = $category;
        $this->orm->persistAndFlush($parentCategory);

        $parentCategory = $this->orm->categoryParents->getBy(["category"=>$category->id]);
        $parentCategory->parent = $values->parent;
        $this->orm->persistAndFlush($parentCategory);

        $this->orm->flush();
    }
}