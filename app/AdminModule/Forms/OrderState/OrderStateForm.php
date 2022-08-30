<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\Model\Order;
use App\Model\OrderLog;
use App\Model\OrderService;
use App\Model\Orm;
use App\Model\Role;
use Contributte\Translation\Translator;
use Latte\Engine;
use Nette;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Tracy\Debugger;

class OrderStateForm extends Nette\Application\UI\Control
{
    private Orm $orm;
    public Order $order;
    public Translator $translator;

    public function __construct(Orm $orm, Translator $translator, Order $order)
    {
        $this->orm = $orm;
        $this->order = $order;
        $this->translator = $translator;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/OrderStateForm.latte');
        $this->template->order = $this->order;
        $this->template->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $states = [];
        $states[Order::ORDER_STATE_RECEIVED] = $this->translator->translate('entity.order.state_'.Order::ORDER_STATE_RECEIVED);
        $states[Order::ORDER_STATE_COMPLETE] = $this->translator->translate('entity.order.state_'.Order::ORDER_STATE_COMPLETE);
        $states[Order::ORDER_STATE_STORNO] = $this->translator->translate('entity.order.state_'.Order::ORDER_STATE_STORNO);

        $form->addSelect('state', 'Stav:', $states)
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        if ($this->order->typeDelivery === Order::TYPE_DELIVERY_ADDRESS || $this->order->typeDelivery === Order::TYPE_DELIVERY_ADDRESS_BIG) {
            $form->addText("toptrans", "TOPTRANS kód")
                ->setDefaultValue($this->order->toptransCode)
                ->setHtmlAttribute('class', 'form-control');
        }

        $form->addSubmit('send', 'Změnit stav objednávky')
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onSuccess[] = [$this, 'onSuccess'];

        return $form;
    }


    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        $this->order->state = $values->state;
        if (isset($values->toptrans)) {
            $this->order->toptransCode = $values->toptrans;
        }
        $this->orm->persistAndFlush($this->order);

        if ($values->state === Order::ORDER_STATE_COMPLETE)
        {
            $this->sendMails($this->order);
        }

        $this->getPresenter()->flashMessage('Stav objednávky byl změněný');
        $this->getPresenter()->redirect('this');

    }

    public function sendMails(Order $order){
        $latte = new Engine();

        $mail = new Message();

        $title = 'Informace o změně stavu objednávky č. ' . $order->id . ' | mopedauto.cz';
        $mail->setFrom(OrderService::FROM_EMAIL)
            ->addTo($order->email)
           // ->addBcc(OrderService::FROM_EMAIL)
            ->setSubject($title)
            ->setHtmlBody($latte->renderToString(__DIR__ . '/../../templates/Emails/orderDone.latte', [
                'order' => $order,
                'basePath' => __DIR__,
                'title' => $title
            ]))
            ->addEmbeddedFile(WWW_DIR . '/images/logo.png');

        $mailer = new SendmailMailer();
        $mailer->send($mail);

        return true;
    }
}