<?php

namespace App\Model;

use Nextras\Dbal\Result\Result;
use Nextras\Orm\Mapper\Mapper;
use Tracy\Debugger;

class ProductCategoriesMapper extends Mapper
{
    public function getProductsInCategories(array $categories): Result
    {
        $builder = $this->connection->query("SELECT product_id FROM product_categories WHERE category_id IN %i[]", $categories);
        return $builder;
    }

    public function getFilteredProductsInCategories(array $categories, int $parameterValueId): Result
    {
        $builder = $this->connection->query("
                                                    SELECT product_categories.product_id FROM product_categories 
                                                    LEFT JOIN products_x_product_parameter_values ON products_x_product_parameter_values.product_id = product_categories.product_id
                                                    WHERE category_id IN %i[]
                                                    AND product_parameter_value_id = %i", $categories, $parameterValueId);
        return $builder;
    }
}