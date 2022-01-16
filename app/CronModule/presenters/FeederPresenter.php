<?php

namespace App\CronModule\Presenters;

use App\CronModule\Model\FeederService;
use App\Model\Orm;

Class FeederPresenter extends BasePresenter {

    /** @var FeederService */
    public $feederService;

    public function __construct(Orm $orm, FeederService $feederService)
    {
        parent::__construct($orm);
        $this->feederService = $feederService;
    }

    public function renderDefault()
    {
        $this->getTemplate()->setFile(__DIR__ . "/../templates/Feeder/default.latte");
    }

    public function renderOneHour()
    {
        $this->feederService->feedStock();
    }

    public function renderDownloadData()
    {
        $this->feederService->downloadData();
    }

    public function renderDownloadStock()
    {
        $this->feederService->downloadStock();
    }

    public function renderOneDayCategories()
    {
        $this->feederService->feedOneHourCategories();
    }

    public function renderOneDayProducts()
    {
        $this->feederService->feedOneHourProducts(); //ok
    }

    public function renderOneDayParents()
    {
        $this->feederService->feedOneHourParents();
    }

    public function renderOneWeek()
    {

    }

    public function renderInit(){
        set_time_limit(1800);
        /*
        echo "start<br>";
        $this->feederService->initCats();
        echo "categories done<br>";
        *///$this->feederService->feedOneHourProducts();/*
        //echo "products done<br>";
        $this->feederService->initImagesAndDescription(); //ok
        //echo "images done<br>";
    }
}