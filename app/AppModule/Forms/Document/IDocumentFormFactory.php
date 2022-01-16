<?php
declare(strict_types=1);

namespace App\AppModule\Forms;

use App\Model\Document;

interface IDocumentFormFactory
{
    function create(?Document $document): DocumentForm;
}