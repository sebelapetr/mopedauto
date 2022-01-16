<?php
declare(strict_types=1);

namespace App\AppModule\Forms;

use App\Model\Partner;

interface IQuotesFormFactory
{
    function create(): QuotesForm;
}