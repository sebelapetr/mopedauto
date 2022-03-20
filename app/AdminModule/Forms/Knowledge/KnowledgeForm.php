<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Document;
use App\Model\Knowledge;
use App\Model\Orm;
use Nette;
use Tracy\Debugger;

class KnowledgeForm extends Nette\Application\UI\Control
{
    private Orm $orm;

    public ?Knowledge $knowledge;

    public function __construct(Orm $orm, ?Knowledge $knowledge)
    {
        $this->orm = $orm;
        $this->knowledge = $knowledge;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/KnowledgeForm.latte');
        $this->template->knowledge = $this->knowledge;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addText('heading', 'Nadpis')
            ->setHtmlAttribute('class', 'form-control');

        $form->addTextArea('description', 'Popis', 6,10)
            ->setHtmlAttribute('class', 'form-control');


        $form->addSubmit('send', $this->knowledge ? 'Upravit' : 'Přidat')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onSuccess[] = [$this, 'onSuccess'];

        if ($this->knowledge)
        {
            $form->setDefaults($this->knowledge->toArray());
        }

        return $form;
    }


    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        if (!$this->knowledge)
        {
            $knowledge = new Knowledge();
        } else {
            $knowledge = $this->knowledge;
        }

        $knowledge->heading = $values->heading;
        $knowledge->description = $values->description;

        $this->orm->persistAndFlush($knowledge);

        if ($this->knowledge) {
            $this->getPresenter()->flashMessage('Vlákno bylo upraveno');
        } else {
            $this->getPresenter()->flashMessage('Vlákno bylo přidáno');
        }

        $this->getPresenter()->redirect('default');
    }
}