<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:21
 */

declare(strict_types=1);

namespace App\AppModule\Presenters;

use App\AppModule\Components\Datagrids\DocumentsDatagrid;
use App\AppModule\Components\Datagrids\PartnerBranchesDatagrid;
use App\AppModule\Components\Datagrids\PartnerOrdersDatagrid;
use App\AppModule\Components\Datagrids\PartnersDatagrid;
use App\AppModule\Components\DocumentsDatagridFactory;
use App\AppModule\Components\PartnerBranchesDatagridFactory;
use App\AppModule\Components\PartnerOrdersDatagridFactory;
use App\AppModule\Components\PartnersDatagridFactory;
use App\AppModule\Forms\IPartnerFormFactory;
use App\AppModule\Forms\PartnerForm;
use App\Model\Partner;
use Tracy\Debugger;

class PartnersPresenter extends BaseAppPresenter
{
    /** @inject */
    public PartnersDatagridFactory $partnersDatagridFactory;

    /** @inject */
    public PartnerBranchesDatagridFactory $partnerBranchesDatagridFactory;

    /** @inject */
    public PartnerOrdersDatagridFactory $partnerOrdersDatagridFactory;

    /** @inject */
    public DocumentsDatagridFactory $documentsDatagridFactory;

    /** @inject */
    public IPartnerFormFactory $partnerFormFactory;

    public ?Partner $partner;

    public function actionEdit(int $id = null): void
    {
        if ($id) {
            try {
                $this->partner = $this->orm->partners->getById($id);
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }
        } else {
            $this->partner = null;
        }
    }

    public function renderEdit(): void
    {
        $this->template->item = $this->partner;
    }

    public function actionDetail(int $id = null): void
    {
        if ($id) {
            try {
                $this->partner = $this->orm->partners->getById($id);
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }
        } else {
            $this->partner = null;
        }
    }

    public function renderDetail(): void
    {
        $this->template->item = $this->partner;
    }

    public function createComponentPartnersDatagrid(string $name): PartnersDatagrid
    {
        return $this->partnersDatagridFactory->create();
    }

    public function createComponentPartnerBranchesDatagrid(string $name): PartnerBranchesDatagrid
    {
        return $this->partnerBranchesDatagridFactory->create($this->partner);
    }

    public function createComponentPartnerOrdersDatagrid(string $name): PartnerOrdersDatagrid
    {
        return $this->partnerOrdersDatagridFactory->create($this->partner);
    }

    public function createComponentPartnerDocumentsDatagrid(string $name): DocumentsDatagrid
    {
        return $this->documentsDatagridFactory->create($this->partner);
    }

    public function createComponentPartnerForm(): PartnerForm
    {
        return $this->partnerFormFactory->create($this->partner);
    }
}