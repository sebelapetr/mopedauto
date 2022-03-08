<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Knowledge;

interface IKnowledgeFormFactory
{
    function create(?Knowledge $knowledge): KnowledgeForm;
}