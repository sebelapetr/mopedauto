<?php

namespace App\CronModule\Presenters;

use App\FrontModule\Forms\INewsletterFormFactory;
use Nette\Application\UI\Presenter;
use App\Model\Orm;

class BasePresenter extends Presenter{

    /** @var Orm  */
    protected $orm;

    public function __construct(Orm $orm)
    {
        parent::__construct();
        $this->orm = $orm;
    }
}