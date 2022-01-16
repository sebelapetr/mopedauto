<?php

namespace App\Model;

/**
 * Class CategoryParent
 * @package App\Model
 * @property int $id {primary}
 * @property Category $category {m:1 Category::$category}
 * @property Category|NULL $parent {m:1 Category::$parent}
 * @property int $categoryId {virtual}
 * @property int|NULL $visible
 * @property int|NULL $priority
 */
use Nextras\Orm\Entity\Entity;

class CategoryParent extends Entity{

    public function getterCategoryId(){
        return $this->category->id;
    }
}