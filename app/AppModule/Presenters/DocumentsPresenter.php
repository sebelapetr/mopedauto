<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:25
 */

declare(strict_types=1);

namespace App\AppModule\Presenters;

use App\AppModule\Components\DocumentsDatagridFactory;
use App\AppModule\Forms\IDocumentFormFactory;
use App\Model\Document;
use Nette\ComponentModel\IComponent;
use Tracy\Debugger;

class DocumentsPresenter extends BaseAppPresenter
{
    /** @inject */
    public DocumentsDatagridFactory $documentsDatagridFactory;

    /** @inject */
    public IDocumentFormFactory $docummentFormFactory;

    public ?Document $document;

    public function actionEdit(int $id = null): void
    {
        if ($id) {
            try {
                $this->document = $this->orm->documents->getById($id);
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }
        } else {
            $this->document = null;
        }
    }

    public function renderEdit(): void
    {
        $this->template->item = $this->document;
    }

    public function createComponentDocumentsDatagrid(string $name): ?IComponent
    {
        return $this->documentsDatagridFactory->create(null);
    }

    public function createComponentDocumentForm(string $name): ?IComponent
    {
        return $this->docummentFormFactory->create($this->document);
    }
}