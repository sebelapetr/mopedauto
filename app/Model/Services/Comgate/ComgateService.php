<?php declare(strict_types=1);

namespace App\Model\Services;

use Brick\Money\Money;
use Contributte\Comgate\Entity\Codes\CountryCode;
use Contributte\Comgate\Entity\Codes\PaymentMethodCode;
use Contributte\Comgate\Entity\Payment;
use Contributte\Comgate\Entity\PaymentStatus;
use Contributte\Comgate\Gateway\PaymentService;
use Contributte\Comgate\Http\Response;
use Nette\SmartObject;
use App\Model\ComgatePayment;
use App\Model\Order;
use App\Model\Orm;
use Tracy\Debugger;

class ComgateService
{
	use SmartObject;

	private Orm $orm;
	private PaymentService $paymentService;

	public bool $isTest;

	public function __construct(Orm $orm, PaymentService $paymentService)
	{
		$this->orm = $orm;
		$this->paymentService = $paymentService;
	}

	public function createPayment(Order $order): Response
	{
		$payment = Payment::of(
			Money::of($order->totalPriceVat, 'CZK'),
            "Objednávka č." . (string) $order->id,
			(string) $order->id,
			$order->email,
			PaymentMethodCode::ALL_CARDS,
			CountryCode::CZ
		);

		return $this->paymentService->create($payment);
	}

	public function logPayment(string $transId, Order $order): ComgatePayment
	{
		$paymentEntity = new ComgatePayment();
		$paymentEntity->transId = $transId;
		$paymentEntity->refId = $order->id;
		$paymentEntity->test = $this->isTest;
		$paymentEntity->status = ComgatePayment::STATE_PENDING;
		$paymentEntity->order = $order;

		$this->orm->persistAndFlush($paymentEntity);

		return $paymentEntity;
	}

	public function checkPayment(string $transId): bool
	{
		$comgatePayment = $this->orm->comgatePayments->getByTransId($transId);

		if (!$comgatePayment) {
			Debugger::log(sprintf('Platba č.%s nebyla nalezena.', $transId), 'comgate-errors');

			return false;
		}

		$res = $this->paymentService->status(PaymentStatus::of($transId));
		$data = $res->getData();

		$comgatePayment->merchantId = $data['merchant'] ?? null;
		$comgatePayment->refId = $data['refId'] ?? null;
		$comgatePayment->price = $data['price'] ?? null;
		$comgatePayment->status = $data['status'];
		$comgatePayment->fee = $data['fee'] ?? null;
		$comgatePayment->email = $data['email'] ?? null;
		$comgatePayment->label = $data['label'] ?? null;
		$this->orm->persistAndFlush($comgatePayment);

		return $comgatePayment->status === ComgatePayment::STATE_PAID;
	}


}