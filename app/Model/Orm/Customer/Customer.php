<?php

namespace App\Model;

use Nextras\Orm\Entity\Entity;

/**
 * Customer
 * @property int $id {primary}
 * @property string $email
 * @property string|NULL $password
 * @property string $name
 * @property string $surname
 * @property bool $active {default false}
 * @property bool $deleted {default false}
 * @property bool $confirmed {default false}
 * @property string|NULL $resetToken
 * @property string|NULL $confirmToken
 * @property string|NULL $phoneNumber
 * @property \DateTimeImmutable|NULL $lastLogin
 * @property \DateTimeImmutable|NULL $createdAt {default now}
 * @property \DateTimeImmutable|NULL $newsletterAgreementAt
 * @property \DateTimeImmutable|NULL $gdprAgreementAt
 * @property string $defaultLang {default 'cs'}
 */
class Customer extends Entity
{

}
