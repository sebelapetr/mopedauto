<?php
declare(strict_types=1);

namespace App\AppModule\Forms;

use App\Model\Orm;
use App\Model\Partner;
use App\Model\Role;
use Nette;

class PartnerForm extends Nette\Application\UI\Control
{
    private Orm $orm;

    public ?Partner $partner;

    public function __construct(Orm $orm, ?Partner $partner)
    {
        $this->orm = $orm;
        $this->partner = $partner;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/PartnerForm.latte');
        $this->template->partner = $this->partner;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addText('name', 'Název')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('partnerFrom', 'Partnerem od')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('email', 'Email')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('phoneNumber', 'Telefon')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('ico', 'IČ')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('dic', 'DIČ')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSelect('partnerUser', 'Uživatel', $this->orm->users->findBy(['role->intName' => Role::INT_NAME_PARTNER])->fetchPairs('id', 'name'))
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('send', $this->partner ? 'Upravit' : 'Přidat')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onSuccess[] = [$this, 'onSuccess'];

        if ($this->partner)
        {
            $form->setDefaults($this->partner->toArray());
        }

        return $form;
    }

    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        if (!$this->partner)
        {
            $partner = new Partner();
        } else {
            $partner = $this->partner;
        }

        $partner->name = $values->name;
        $partner->partnerFrom = $values->partnerFrom;
        $partner->email = $values->email;
        $partner->phoneNumber = $values->phoneNumber;
        $partner->ico = $values->ico;
        $partner->dic = $values->dic;
        $partner->createdAt = new \DateTimeImmutable();
        $partner->adminUser = $this->orm->users->getById($values->partnerUser);

        $this->orm->persistAndFlush($partner);

        if ($this->partner) {
            $this->getPresenter()->flashMessage('Partner byl upravený');
        } else {
            $this->getPresenter()->flashMessage('Partner byl přidaný');
        }

        $this->getPresenter()->redirect('edit', ['id' => $partner->id]);
    }
}