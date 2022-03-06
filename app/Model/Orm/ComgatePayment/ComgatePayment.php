<?php declare(strict_types=1);

namespace App\Model;

use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\OneHasOne;

/**
 * @property-read int $id {primary}
 * @property int|NULL $merchantId
 * @property int|NULL $refId
 * @property string $transId
 * @property bool $test
 * @property int|NULL $price
 * @property int|NULL $curr
 * @property string $status {enum self::STATE_*}
 * @property int|NULL $fee
 * @property string|NULL $email
 * @property string|NULL $label
 * @property OneHasOne|Order $order {1:1 Order::$comgatePayment}
 *
 */
class ComgatePayment extends Entity
{
	public const
		STATE_PENDING = "PENDING",
		STATE_PAID = "PAID",
		STATE_CANCELLED = "CANCELLED";

}
