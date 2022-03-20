<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\City;
use App\Model\Orm;
use Nette;

class CityForm extends Nette\Application\UI\Control
{
    private Orm $orm;

    public ?City $city;

    public function __construct(Orm $orm, ?City $city)
    {
        $this->orm = $orm;
        $this->city = $city;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/CityForm.latte');
        $this->template->city = $this->city;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addText('name', 'Název')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();


        $form->addSubmit('send', $this->city ? 'Upravit' : 'Přidat')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onValidate[] = [$this, 'onValidate'];
        $form->onSuccess[] = [$this, 'onSuccess'];

        if ($this->city)
        {
            $form->setDefaults($this->city->toArray());
        }

        return $form;
    }

    public function onValidate(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();
        if ($this->orm->cities->getBy(['name' => $values->name]) && !$this->city)
        {
            $form->addError('Město se stejným názvem již existuje.');
            return;
        }
    }

    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        if (!$this->city)
        {
            $city = new City();
        } else {
            $city = $this->city;
        }

        $city->name = $values->name;


        $this->orm->persistAndFlush($city);

        if ($this->city) {
            $this->getPresenter()->flashMessage('Město bylo upraveno');
        } else {
            $this->getPresenter()->flashMessage('Město bylo přidáno');
        }

        $this->getPresenter()->redirect('edit', ['id' => $city->id]);
    }
}