<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Orm;
use App\Model\Zip;
use Nette;

class ZipForm extends Nette\Application\UI\Control
{
    private Orm $orm;
    public ?Zip $zip;

    public function __construct(Orm $orm, ?Zip $zip)
    {
        $this->orm = $orm;
        $this->zip = $zip;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/ZipForm.latte');
        $this->template->zip = $this->zip;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addText('zip', 'ZIP')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSelect('city', 'Město', $this->orm->cities->findAll()->fetchPairs('id', 'name'))
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSubmit('send', $this->zip ? 'Upravit' : 'Přidat')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onValidate[] = [$this, 'onValidate'];
        $form->onSuccess[] = [$this, 'onSuccess'];

        if ($this->zip)
        {
            $defaults = $this->zip->toArray();
            unset($defaults['city']);

            $defaults['city'] = $this->zip->city->id;

            $form->setDefaults($defaults);
        }

        return $form;
    }

    public function onValidate(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();
        if ($this->orm->zips->getBy(['zip' => $values->zip]) && !$this->zip)
        {
            $form->addError('PSČ se stejným názvem již existuje.');
            return;
        }
    }

    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        if (!$this->zip)
        {
            $zip = new Zip();
        } else {
            $zip = $this->zip;
        }

        $zip->zip = $values->zip;
        $zip->city = $this->orm->cities->getById($values->city);

        $this->orm->persistAndFlush($zip);

        if ($this->zip) {
            $this->getPresenter()->flashMessage('Město bylo upraveno');
        } else {
            $this->getPresenter()->flashMessage('Město bylo přidáno');
        }

        $this->getPresenter()->redirect('edit', ['id' => $zip->id]);
    }
}