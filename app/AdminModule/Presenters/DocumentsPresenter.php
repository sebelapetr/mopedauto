<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:25
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Datagrids\DocumentsDatagrid;
use App\AdminModule\Components\DocumentsDatagridFactory;
use App\AdminModule\Forms\DocumentForm;
use App\AdminModule\Forms\IDocumentFormFactory;
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

    public function createComponentDocumentsDatagrid(string $name): DocumentsDatagrid
    {
        return $this->documentsDatagridFactory->create(null);
    }

    public function createComponentDocumentForm(string $name): DocumentForm
    {
        return $this->docummentFormFactory->create($this->document);
    }
}