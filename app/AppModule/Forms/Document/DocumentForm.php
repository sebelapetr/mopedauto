<?php
declare(strict_types=1);

namespace App\AppModule\Forms;

use App\Model\City;
use App\Model\Document;
use App\Model\Orm;
use Nette;

class DocumentForm extends Nette\Application\UI\Control
{
    private Orm $orm;

    public ?Document $document;

    public function __construct(Orm $orm, ?Document $document)
    {
        $this->orm = $orm;
        $this->document = $document;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/DocumentForm.latte');
        $this->template->document = $this->document;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addText('name', 'Název dokumentu')
            ->setHtmlAttribute('class', 'form-control');

        $form->addUpload('file', 'Soubor');

        //todo prirazeni entity

        $form->addSubmit('send', $this->document ? 'Upravit' : 'Přidat')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onSuccess[] = [$this, 'onSuccess'];

        if ($this->document)
        {
            $form->setDefaults($this->document->toArray());
        }

        return $form;
    }

    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        if (!$this->document)
        {
            $document = new Document();
        } else {
            $document = $this->document;
        }

        $document->name = $values->name;


        $this->orm->persistAndFlush($document);

        if ($this->document) {
            $this->getPresenter()->flashMessage('Město bylo upraveno');
        } else {
            $this->getPresenter()->flashMessage('Město bylo přidáno');
        }

        $this->getPresenter()->redirect('edit', ['id' => $document->id]);
    }
}