<?php

namespace App\Model;

use Nextras\Orm\Mapper\Mapper;

class QuotesMapper extends Mapper{

    public function findCategoriesNew(){
        $builder = $this->connection->query(
            "SELECT * FROM `quotes`"
        );
        return $builder;
    }
}