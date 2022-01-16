<?php
declare(strict_types=1);

namespace App\AppModule\Forms;

use App\Model\Zip;

interface IZipFormFactory
{
    function create(?Zip $zip): ZipForm;
}