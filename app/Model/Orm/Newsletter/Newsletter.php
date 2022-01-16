<?php

namespace App\Model;

use Nette\Utils\DateTime;
use Nextras\Dbal\Utils\DateTimeImmutable;
use Nextras\Orm\Entity\Entity;

/**
 * Class Newsletter
 * @package App\Model
 * @property int $id {primary}
 * @property string $email
 * @property DateTimeImmutable $date
 * @property int $allowed
 */
Class Newsletter extends Entity{

}