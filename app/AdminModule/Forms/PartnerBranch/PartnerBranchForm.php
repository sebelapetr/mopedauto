<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Orm;
use App\Model\PartnerBranch;
use App\Model\Role;
use Nette;
use Tracy\Debugger;

class PartnerBranchForm extends Nette\Application\UI\Control
{
    private Orm $orm;

    public ?PartnerBranch $partnerBranch;

    public function __construct(Orm $orm, ?PartnerBranch $partnerBranch)
    {
        $this->orm = $orm;
        $this->partnerBranch = $partnerBranch;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/PartnerBranchForm.latte');
        $this->template->partnerBranch = $this->partnerBranch;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addText('name', 'Název')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('email', 'Email')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSelect('partner', 'Partner', $this->orm->partners->findAll()->fetchPairs('id', 'name'))
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('phoneNumber', 'Telefon')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('addressNumber', 'Číslo popisné')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('street', 'Ulice')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSelect('city', 'Město', $this->orm->cities->findAll()->fetchPairs('id', 'name'))
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSelect('zip', 'PSČ', $this->orm->zips->findAll()->fetchPairs('id', 'zip'))
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSelect('branchUser', 'Uživatel', $this->orm->users->findBy(['role->intName' => Role::INT_NAME_BRANCH])->fetchPairs('id', 'name'))
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('send', $this->partnerBranch ? 'Upravit' : 'Přidat')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onSuccess[] = [$this, 'onSuccess'];

        if ($this->partnerBranch)
        {
            $defaults = $this->partnerBranch->toArray();

            unset($defaults['partner']);
            unset($defaults['city']);
            unset($defaults['zip']);
            unset($defaults['branchUser']);

            $defaults['partner'] = $this->partnerBranch->partner->id;
            $defaults['city'] = $this->partnerBranch->city->id;
            $defaults['zip'] = $this->partnerBranch->zip->id;
            $defaults['branchUser'] = $this->partnerBranch->branchUser->id;

            $form->setDefaults($defaults);
        }

        return $form;
    }

    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        if (!$this->partnerBranch)
        {
            $partnerBranch = new PartnerBranch();
        } else {
            $partnerBranch = $this->partnerBranch;
        }

        $partnerBranch->name = $values->name;
        $partnerBranch->email = $values->email;
        $partnerBranch->phoneNumber = $values->phoneNumber;
        $partnerBranch->partner = $this->orm->partners->getById($values->partner);
        $partnerBranch->addressNumber = $values->addressNumber;
        $partnerBranch->street = $values->street;
        $partnerBranch->city = $values->city;
        $partnerBranch->zip = $values->zip;
        $partnerBranch->branchUser = $this->orm->users->getById($values->branchUser);
        $partnerBranch->createdAt = new \DateTimeImmutable();

        $this->orm->persistAndFlush($partnerBranch);

        if ($this->partnerBranch) {
            $this->getPresenter()->flashMessage('Pobočka byla upraveno');
        } else {
            $this->getPresenter()->flashMessage('Pobočka byla přidáno');
        }

        $this->getPresenter()->redirect('edit', ['id' => $partnerBranch->id]);
    }
}