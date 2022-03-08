<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 24. 9. 2020
 * Time: 20:36
 */

declare(strict_types=1);

namespace App\Model;

use Nextras\Orm\Repository\Repository;

class SettingsRepository extends Repository
{

    public static function getEntityClassNames(): array
    {
        return [Setting::class];
    }

}