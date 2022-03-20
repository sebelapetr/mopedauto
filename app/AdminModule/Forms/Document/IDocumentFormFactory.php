<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Document;

interface IDocumentFormFactory
{
    function create(?Document $document): DocumentForm;
}