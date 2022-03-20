<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\PartnerBranch;

interface IPartnerBranchFormFactory
{
    function create(?PartnerBranch $partnerBranch): PartnerBranchForm;
}