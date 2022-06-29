<?php

namespace App\FrontModule\Forms;

use App\Model\AddProductService;
use App\Model\FindService;
use App\Model\QuoteService;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Tracy\Debugger;


class FindForm extends Control{

    /** @var FindService */
    public $findService;


    public function __construct(FindService $findService)
    {
        $this->findService = $findService;
    }

    protected function createComponentFindForm(){
        $form = new Form();
        $form->addText('phrase');
        $form->addSubmit("submit");
        $form->onSuccess[] = [$this, 'findFormSucceeded'];
        return $form;
    }
    public function findFormSucceeded(Form $form, $values){
        $this->getPresenter()->redirect('SpareParts:search', ["phrase" => $values->phrase]);
    }
    public function render(){
        $this->getTemplate()->setFile(__DIR__  .  "/../../forms/Find/Find.latte");
        $this->getTemplate()->render();
    }

}