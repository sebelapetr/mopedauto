<?php

namespace App\Model;

use Nextras\Dbal\Result\Result;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Mapper\Mapper;

class CategoriesMapper extends Mapper
{
    public function getParents(int $categoryId): Result
    {
        $builder = $this->connection->query("
            WITH RECURSIVE parents AS (
              SELECT id, category_name, parent_id, seo_name, 0 AS level
              FROM categories
              WHERE id = %i
            
              UNION ALL
            
              SELECT cat.id, cat.category_name, cat.parent_id, cat.seo_name, p.level - 1
              FROM categories cat, parents p
              WHERE cat.id = p.parent_id
            )
            SELECT id, category_name as name, level, CONCAT_WS('-', id, seo_name) AS url FROM parents
            ORDER BY level ASC;
        ", $categoryId);
        return $builder;
    }

    public function getChildren(int $categoryId): Result
    {
        $builder = $this->connection->query("
            WITH RECURSIVE parents AS (
              SELECT id, category_name, parent_id, seo_name, 0 AS level
              FROM categories
              WHERE id = %i
            
              UNION ALL
            
              SELECT cat.id, cat.category_name, cat.parent_id, cat.seo_name, p.level - 1
              FROM categories cat, parents p
              WHERE p.id = cat.parent_id
            )
            SELECT id, category_name as name, parent_id as parent, level, CONCAT_WS('-', id, seo_name) AS url FROM parents;
        ", $categoryId);
        return $builder;
    }

    public function getChildrenLevel(int $categoryId, int $level): Result
    {
        $builder = $this->connection->query("
            WITH RECURSIVE parents AS (
              SELECT id, category_name, parent_id, seo_name, 0 AS level, product_parameter_value_id
              FROM categories
              WHERE id = %i
            
              UNION ALL
            
              SELECT cat.id, cat.category_name, cat.parent_id, cat.seo_name, p.level - 1, cat.product_parameter_value_id
              FROM categories cat, parents p
              WHERE p.id = cat.parent_id
            )
            SELECT id, category_name as name, parent_id as parent, level, CONCAT_WS('-', id, seo_name) AS url, product_parameter_value_id as parValue FROM parents
            WHERE level = %i;
        ", $categoryId, $level);
        return $builder;
    }

    public function getRoots(): Result
    {
        $builder = $this->connection->query("
          WITH RECURSIVE categories_with_roots AS (
              SELECT id, parent_id, category_name, category_name as root_name
              FROM categories
              WHERE parent_id IS NULL
            
              UNION ALL
            
              SELECT cat.id, cat.parent_id, cat.category_name, cwr.root_name
              FROM categories cat, categories_with_roots cwr
              WHERE cat.parent_id = cwr.id
            )
            SELECT category_name, root_name FROM categories_with_roots;
        ");
        return $builder;
    }

    public function getProductsRoot()
    {
        $builder = $this->connection->query("
          WITH RECURSIVE categories_with_roots AS (
              SELECT id, parent_id, category_name, category_name as root_name
              FROM categories
              WHERE parent_id IS NULL
            
              UNION ALL
            
              SELECT cat.id, cat.parent_id, cat.category_name, cwr.root_name
              FROM categories cat, categories_with_roots cwr
              WHERE cat.parent_id = cwr.id
            )
            SELECT p.product_name AS product_name, cwr.root_name
            FROM products p, categories_with_roots cwr
            WHERE p.category = cwr.id;
        ");
        return $builder;
    }

    public function getProductRoot($productId)
    {
        $builder = $this->connection->query("
           WITH RECURSIVE categories_with_roots AS (
              SELECT id, parent_id, category_name, category_name as root_name
              FROM categories
              WHERE parent_id IS NULL
            
              UNION ALL
            
              SELECT cat.id, cat.parent_id, cat.category_name, cwr.root_name
              FROM categories cat, categories_with_roots cwr
              WHERE cat.parent_id = cwr.id
            )
            SELECT p.product_name AS product_name, cwr.root_name
            FROM products p, categories_with_roots cwr
            WHERE p.category = cwr.id
                AND p.id= %i ;
        ", $productId);
        return $builder;
    }
}