<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\City;
use App\Model\Document;
use App\Model\Orm;
use Nette;
use Tracy\Debugger;

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

        $form->addUpload('file', 'Soubor')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSelect('partner', 'Partner', $this->orm->partners->findBy(['deleted' => false])->fetchPairs('id', 'name'))
            ->setHtmlAttribute('class', 'form-control')
            ->setPrompt('');

        $form->addSubmit('send', $this->document ? 'Upravit' : 'Přidat')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onSuccess[] = [$this, 'onSuccess'];
        $form->onValidate[] = [$this, 'onValidate'];

        if ($this->document)
        {
            $defaults = $this->document->toArray();
            unset($defaults['partner']);
            $defaults['partner'] = $this->document->partner ? $this->document->partner->id : null;
            $form->setDefaults($defaults);
        }

        return $form;
    }

    public function onValidate(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();
        /** @var Nette\Http\FileUpload $file */
        $file = $values->file;
        if ($file->getSize() <= 0) {
            $form->addError('Chyba při nahrání souboru');
            return;
        }
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
        $document->partner = $values->partner ? $this->orm->partners->getById($values->partner) : null;

        $soubor = $values->file;
        $now = new \DateTimeImmutable();
        $soubor->move("upload/documents/" . $values->file->name);

        if($soubor->isOk()) {
            $ext = pathinfo($values->file->name, PATHINFO_EXTENSION);
            $fileName = basename($values->file->name,".".$ext);
            $soubor->move('upload/documents/' . $values->file->name . $now->format('Y-m-dHis').'.'.$ext);
            $document->filePath = $values->file->name . $now->format('Y-m-dHis').'.'.$ext;
        }
        $this->orm->persistAndFlush($document);

        if ($this->document) {
            $this->getPresenter()->flashMessage('Dokument byl upravený');
        } else {
            $this->getPresenter()->flashMessage('Dokument byl přidaný');
        }

        $this->getPresenter()->redirect('edit', ['id' => $document->id]);
    }
}