<?php declare(strict_types=1);

namespace App\FrontModule\Presenters;

use Nette\Application\LinkGenerator;
use Nette\Application\Responses\VoidResponse;
use App\Model\Order;
use App\Model\OrderService;
use App\Model\Orm;
use App\Model\Services\ComgateService;
use App\Presenters\BasePresenter;
use Nextras\Dbal\Utils\DateTimeImmutable;
use Tracy\Debugger;

class ComgatePresenter extends BasePresenter
{
	/** @inject */
	public ComgateService $comgateService;

	/** @inject */
	public Orm $orm;

	/** @inject */
	public OrderService $orderService;

	/** @inject */
	public LinkGenerator $linkGenerator;

	public function actionPaymentResult($id, $refId): void
	{
		$redirectAfter = true;
		if ($id === null)
		{
			$redirectAfter = true;
			$post = $this->getHttpRequest()->getPost();
			if (isset($post['transId'])) {
				$id = $post['transId'];
			} else {
				Debugger::log('Nenalezena transakce: ', 'comgate-errors');
				Debugger::log($post, 'comgate-errors');
				throw new \InvalidArgumentException("Transaction was not found.");
			}
		}

		$paymentCheckSuccess = $this->comgateService->checkPayment($id);

		if ($paymentCheckSuccess)
		{
			$comgatePayment = $this->orm->comgatePayments->getByTransId($id);
			$order = $comgatePayment->order;

			if($order->state === Order::ORDER_STATE_UNFINISHED){
				$order->state = Order::ORDER_STATE_RECEIVED;
                $order->createdAt = new DateTimeImmutable();
				$this->orm->persistAndFlush($order);

                $orderSent = $this->orderService->sendMails($order);
                if ($orderSent)
                {
                    $this->cartService->reset();
                    $shippingSection = $this->getPresenter()->getSession()->getSection('shipping');
                    unset($shippingSection->shipping);
                    $paymentSection = $this->getPresenter()->getSession()->getSection('payment');
                    unset($paymentSection->payment);
                }
			}

			if($redirectAfter) {
				$this->flashMessage('Platba byla úspěšně ověřena.');
				$this->redirect(':Front:Cart:step3', base64_encode(strval($order->id)).'8452');
			}else{
				$this->sendResponse(new VoidResponse());
			}
		} else {
			if($redirectAfter) {
				$this->flashMessage('Chyba při ověřování platby.');
				$this->redirect(':Front:Cart:step2');
			}else{
				$this->sendResponse(new VoidResponse());
			}
		}
	}
}
