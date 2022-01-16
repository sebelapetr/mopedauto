<?php

namespace App\Model;

use Nextras\Orm\Mapper\Mapper;
use function PHPSTORM_META\type;
use Tracy\Debugger;

class ProductsMapper extends Mapper{

    public function insertProduct($values){
        if($values->recept = true){
            $values->recept = 1;
        }
        else{
            $values->recept = 0;
        }
        if($values->inStock = true){
            $values->inStock = 1;
        }
        else{
            $values->inStock = 0;
        }
        $categoryId = $values->category->id;
        //INSERT INTO table (id, name) VALUES (id1, name1), (id2, name2), ..., (idN, nameN) ON DUPLICATE KEY UPDATE id=VALUES(id), name=VALUES(name)
        $value = "$values->id".', "'."$values->productName".'", "'."$values->brandName".'", "'."$categoryId".'", "'."$values->vendorInternalId".'", "'."$values->vendorName".'", "'."$values->ean".'", "'."$values->catalogPrice".'", "'."$values->catalogPriceVat".'", "'."$values->clientPrice".'", "'."$values->clientPriceVat".'", "'."$values->retailPrice".'", "'."$values->vat".'", "'."$values->inStock".'", "'."$values->minStockLevel".'", "'."$values->stockLevel".'", "'."$values->weight".'", "'."$values->recept".'", "'."1".'", "'."$values->sukl".'", "'."$values->pdkId".'", "'."$values->priceList".'", "'."$values->restrictions".'", "'."$values->tooOrder".'"';
        $builder = $this->connection->query(
            "INSERT INTO products (id, product_name, brand_name, category, vendor_internal_id, vendor_name, ean, catalog_price, catalog_price_vat, client_price, client_price_vat, retail_price, vat, in_stock, min_stock_level, stock_level, weight, recept, active, sukl, pdk_id, price_list, restrictions, too_order)
                   VALUES (".$value.")
                   ON DUPLICATE KEY UPDATE id=VALUES(id), product_name = VALUES(product_name), brand_name = VALUES(brand_name), category = VALUES(category), vendor_internal_id = VALUES(vendor_internal_id), vendor_name = VALUES(vendor_name), ean = VALUES(ean), catalog_price = VALUES(catalog_price), catalog_price_vat = VALUES(catalog_price_vat), client_price = VALUES(client_price), client_price_vat = VALUES(client_price_vat), retail_price = VALUES(retail_price), vat = VALUES(vat), in_stock = VALUES(in_stock), min_stock_level = VALUES(min_stock_level), stock_level = VALUES(stock_level), weight = VALUES(weight), recept = VALUES(recept), active = VALUES(active), sukl = VALUES(sukl), pdk_id = VALUES(pdk_id), price_list = VALUES(price_list), restrictions = VALUES(restrictions), too_order = VALUES(too_order)
        ");
        return $builder;
    }

    public function insertStock($values){
        $value = "$values->id".', "'."$values->minStockLevel".'"';
        $builder = $this->connection->query("
            INSERT INTO products (id, min_stock_level)
            VALUES (".$value.")
            ON DUPLICATE KEY UPDATE id=VALUES(id), min_stock_level = VALUES(min_stock_level)
        ");
        return $builder;
    }

    public function findProducts($p){
        $find = explode(' ',$p);
        //$find = implode('%', $find);
        //$builder = $this->connection->query('SELECT * FROM products WHERE product_name LIKE %_like_', html_entity_decode($p));
        $builder = $this->connection->createQueryBuilder();
        $builder->select('*');
        $builder->from('products');
        $builder->where('product_name LIKE %_like_ ',$p);
        $y='';
        foreach ($find as $item) {
            $builder->orWhere('product_name = %s',$item);
            $y = $y.'%'.$item;
        }
        $builder->orWhere('product_name = %_like_', $y);
        $builder->limitBy(1000);
        $result = $this->connection->queryArgs(
            $builder->getQuerySql(),
            $builder->getQueryParameters()
        );
        // SELECT * FROM products WHERE MATCH(product_name) AGAINST('first mate cat' IN BOOLEAN MODE) OR MATCH(description) AGAINST('first mate cat' IN BOOLEAN MODE)
        /*
            foreach ($find as $item) {
            $builder = $this->connection->query('SELECT * FROM products WHERE product_name LIKE %_like_', $item);
            $obj_merged = array_merge((array) $obj_merged, (array) $builder);
        }
        */

        return $result;
    }
}