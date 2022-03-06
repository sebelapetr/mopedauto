<?php declare(strict_types=1);

namespace App\Model;

use Nextras\Orm\Repository\Repository;

class ComgatePaymentsRepository extends Repository
{
	public static function getEntityClassNames(): array
	{
		return [ComgatePayment::class];
	}

	public function getByTransId(string $transId): ?ComgatePayment
	{
		return $this->getBy(['transId' => $transId]);
	}

}