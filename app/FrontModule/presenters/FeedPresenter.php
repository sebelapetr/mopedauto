<?php

namespace App\FrontModule\presenters;

use App\FrontModule\Forms\IContactFormFactory;
use App\Model\Orm;
use App\Model\Services\Feeds\GoogleFeedService;
use Nette\Application\BadRequestException;
use Nette\ComponentModel\IComponent;

Class FeedPresenter extends BasePresenter
{
    /** @inject  */
    public IContactFormFactory $contactFormFactory;

    public $types = [
        "google.xml"
    ];

    /** @inject */
    public GoogleFeedService $googleFeedService;

    public function actionDefault($typeXml){
        if (!in_array($typeXml, $this->types)) {
            die;
        }

        Header('Content-type: text/xml');

        if ($typeXml == "google.xml") {
            $xml = $this->googleFeedService->generate();
            echo $xml->build();
        }

        $this->getTemplate()->setFile(__DIR__ . "/../templates/Feed/default.latte");
    }

    public function createComponentContactForm(string $name): ?IComponent
    {
        return $this->contactFormFactory->create();
    }
}