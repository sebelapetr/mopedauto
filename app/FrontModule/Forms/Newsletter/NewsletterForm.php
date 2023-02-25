<?php

namespace App\FrontModule\Forms;

use App\Model\AddProductService;
use App\Model\NewsletterService;
use App\Model\QuoteService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Tracy\Debugger;

interface INewsletterFormFactory{
    /** @return NewsletterForm */
    function create();
}

class NewsletterForm extends Control{

    /** @var NewsletterService */
    public $newsletterService;

    public function __construct(NewsletterService $newsletterService)
    {
        $this->newsletterService = $newsletterService;
    }

    protected function createComponentNewsletterForm(){
        $form = new Form();
        $form->addEmail('email');
        $form->addSubmit("submit");
        $form->onSuccess[] = [$this, 'newsletterFormSucceeded'];
        return $form;
    }
    public function newsletterFormSucceeded(Form $form, $values){

        $this->newsletterService->submitEmail($values);
        $this->getPresenter()->flashMessage("Registrace byla úspěšná.");

        //$this->getPresenter()->redirect("Homepage:default");
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../Forms/Newsletter/Newsletter.latte");
        $this->getTemplate()->render();
    }

}