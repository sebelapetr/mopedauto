<?php

namespace App\Model;

use Tracy\Debugger;
use Nette\Http\Url;

class EditParentCategoryService{

    /** @var Orm */
    private $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function getId(){

    }
    public function getCategory(){
        $categories = $this->orm->categories->getById(5);
        return $categories;
    }

    public function getCategories(){
        $categories = $this->orm->categories->findAll();
        foreach ($categories as $category){
            $categorie[$category->id] = $category->name;
        }
        return $categorie;
    }

    public function editParentCategory($values){
        $parentCategory = $this->orm->categoryParents->getBy(["category"=>$values->id]);
        $parentCategory->parent = $values->parent;
        $this->orm->persistAndFlush($parentCategory);
    }
}