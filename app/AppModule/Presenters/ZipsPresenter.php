<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:23
 */

declare(strict_types=1);

namespace App\AppModule\Presenters;

use App\AppModule\Components\Datagrids\ZipsDatagrid;
use App\AppModule\Components\ZipsDatagridFactory;
use App\AppModule\Forms\IZipFormFactory;
use App\AppModule\Forms\ZipForm;
use App\Model\Zip;
use Tracy\Debugger;

class ZipsPresenter extends BaseAppPresenter
{

    /** @inject */
    public ZipsDatagridFactory $zipsDatagridFactory;

    /** @inject */
    public IZipFormFactory $zipFormFactory;

    public ?Zip $zip;

    public function actionEdit(int $id = null): void
    {
        if ($id) {
            try {
                $this->zip = $this->orm->zips->getById($id);
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }
        } else {
            $this->zip = null;
        }
    }

    public function renderEdit(): void
    {
        $this->template->item = $this->zip;
    }

    public function createComponentZipsDatagrid(string $name): ZipsDatagrid
    {
        return $this->zipsDatagridFactory->create();
    }

    public function createComponentZipForm(): ZipForm
    {
        return $this->zipFormFactory->create($this->zip);
    }

    public function handleDelete(int $id): void
    {
        $item = $this->orm->zips->getById($id);

        if ($item)
        {
            $item->deleted = true;
            $this->orm->persistAndFlush($item);
        }

        if ($this->isAjax()) {
            $this['zipsDatagrid']->reload();
        } else {
            $this->redirect('this');
        }
    }
}