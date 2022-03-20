<?php

namespace App\Model;

use Nextras\Orm\Entity\Entity;

/**
 * User
 * @property int $id {primary}
 * @property string $email
 * @property string|NULL $password
 * @property string $name
 * @property string $surname
 * @property Role|NULL $role {m:1 Role::$users}
 * @property bool $active {default false}
 * @property string|NULL $resetToken
 * @property string|NULL $phoneNumber
 * @property \DateTimeImmutable|NULL $lastLogin
 * @property \DateTimeImmutable|NULL $createdAt {default now}
 * @property string $defaultLang {default 'cs'}
 * @property bool|NULL $workingNow
 */
class User extends Entity
{

}
