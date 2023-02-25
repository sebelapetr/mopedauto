<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Category;
use App\Model\Enum\FlashMessages;
use App\Model\Orm;
use App\Model\Role;
use Nette;

class CategoryForm extends Nette\Application\UI\Control
{
    private Orm $orm;

    public ?Category $category;

    public function __construct(?Category $category,Orm $orm)
    {
        $this->orm = $orm;
        $this->category = $category;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/CategoryForm.latte');
        $this->template->category = $this->category;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addText('name', 'Název')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('seoName', 'SEO Název')
            ->setHtmlAttribute('class', 'form-control');

        $form->addTextArea('description', 'Popis')
            ->setHtmlAttribute('class', 'form-control');

        $form->addCheckbox('visible', 'Viditelná')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('send', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onSuccess[] = [$this, 'onSuccess'];

        $form->setDefaults([
            'name' => $this->category->categoryName,
            'seoName' => $this->category->seoName,
            'description' => $this->category->description,
            'visible' => $this->category->visible,
        ]);

        return $form;
    }

    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        $category = $this->category;
        $category->categoryName = $values->name;
        $category->seoName = $values->seoName;
        $category->description = $values->description;
        $category->visible = $values->visible;

        $this->orm->persistAndFlush($category);

        $this->getPresenter()->flashMessage('Kategorie byla upravená', FlashMessages::SUCCESS);

        $this->getPresenter()->redirect('default', ['id' => $category->id]);
    }
}