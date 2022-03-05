<?php

namespace App\Model;

use Nextras\Dbal\Result\Result;
use Nextras\Orm\Mapper\Mapper;

class ProductCategoriesMapper extends Mapper
{
    public function getProductsInCategories(array $categories): Result
    {
        $builder = $this->connection->query("SELECT product_id FROM product_categories WHERE category_id IN %i[]", $categories);
        return $builder;
    }
}