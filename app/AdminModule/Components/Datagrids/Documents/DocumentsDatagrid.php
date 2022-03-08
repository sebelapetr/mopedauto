<?php
declare(strict_types=1);

namespace App\AdminModule\Components\Datagrids;

use App\Model\Document;
use App\Model\Orm;
use App\Model\Partner;
use Nette;
use Nette\Utils\Html;

class DocumentsDatagrid extends BasicDatagrid
{
    protected Orm $orm;
    private ?Partner $partner;

    public function __construct(Orm $orm, ?Partner $partner, Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($orm, $parent, $name);

        $this->orm = $orm;
        $this->partner = $partner;
    }

    public function setup(): void
    {
        $domain = "entity.document";

        if ($this->partner)
            $this->setDataSource($this->orm->documents->findBy(['partner' => $this->partner])->orderBy('createdAt', 'DESC'));
        else
            $this->setDataSource($this->orm->documents->findAll()->orderBy('createdAt', 'DESC'));

        $this->addColumnText("createdAt", $domain.".createdAt")
            ->setRenderer(function (Document $item) {
                return $item->createdAt->format('d.m.Y');
            })
            ->setSortable()
            ->setFilterDate();

        $this->addColumnText("file", $domain.".file")
            ->setRenderer(function (Document $item) {
                return Html::el('a', ['href' => $this->getPresenter()->getHttpRequest()->getUrl()->getBaseUrl().'upload/documents/' . $item->filePath, 'target' => '_blank'])->setText($item->filePath ? Nette\Utils\Strings::truncate($item->filePath, 40) : 'Stáhnout');
            })
            ->setSortable()
            ->setFilterText();

        if (!$this->partner) {
            $this->addColumnText("partner", $domain . ".partner")
                ->setSortable()
                ->setRenderer(function (Document $item) {
                    if ($item->partner) {
                        return Html::el('a', [
                            'href' => $this->getPresenter()->link('Partners:detail', ['id' => $item->partner->id]),
                            'target' => '_blank'
                        ])->setText($item->partner->name);
                    } else {
                        return '-';
                    }
                })
                ->setFilterSelect([null => ''] + $this->orm->partners->findAll()->fetchPairs('id', 'name'));
        }
    }
}