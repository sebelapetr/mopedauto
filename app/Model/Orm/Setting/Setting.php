<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 24. 9. 2020
 * Time: 20:35
 */

declare(strict_types=1);

namespace App\Model;

use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\HasMany;
use App\Model\Enum\SettingsEnum;
use Tracy\Debugger;

/**
 * Class Setting
 * @package App\Model
 * @property int $id {primary}
 * @property string $option {enum SettingsEnum::OPTION_*}
 * @property string|null $value
 * @property bool $showInSettings
 * @property string $type {enum self::TYPE_*}
 */
class Setting extends Entity
{
    public const TYPE_BOOLEAN = "BOOLEAN";
    public const TYPE_STRING = "STRING";

    public function getValueWithType()
    {
        if ($this->type === self::TYPE_BOOLEAN)
        {
            return boolval($this->value);
        } elseif ($this->type === self::TYPE_STRING)
        {
            return strval($this->value);
        }
    }
}