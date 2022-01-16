<?php

namespace App\FrontModule\Presenters;

use App\Model\Orm;

Class HomepagePresenter extends BasePresenter
{

    public function renderDefault(){
        $this->getTemplate()->setFile(__DIR__ . "/../templates/Homepage/default.latte");
    }

}