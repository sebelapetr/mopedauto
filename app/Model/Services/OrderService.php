<?php

namespace App\Model;

use Latte\Engine;
use Nette\Caching\Storages\FileStorage;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use ondrs\Hi\Hi;
use Tracy\Debugger;

class OrderService
{

    /** @var Orm */
    private $orm;

    /** @var InvoiceService */
    public $invoiceService;

    public const FROM_EMAIL = 'mopedauto.cz <info@mopedauto.cz>';
    public const NO_REPLY_EMAIL = 'mopedauto.cz <noreply@mopedauto.cz>';

    public function __construct(Orm $orm, InvoiceService $invoiceService)
    {
        $this->orm = $orm;
        $this->invoiceService = $invoiceService;
    }

    public function newOrder($values, $sessionProducts, $sessionShipping, $sessionPayment, $sessionOrder)
    {
        if (!$sessionProducts) {
           return;
        }
        $order = new Order();
        $order->name = $values->name;
        $order->surname = $values->surname;
        $order->telephone = $values->telephone;
        $order->email = $values->email;
        $order->street = $values->street;
        $order->city = $values->city;
        $order->psc = $values->psc;
        $order->note = $values->note;
        $order->company = $values->company;
        $order->ico = $values->ico;
        $order->dic = $values->dic;
        $order->deliveryName = $values->deliveryName;
        $order->deliverySurname = $values->deliverySurname;
        $order->deliveryCompany = $values->deliveryCompany;
        $order->deliveryStreet = $values->deliveryStreet;
        $order->deliveryCity = $values->deliveryCity;
        $order->deliveryPsc = $values->deliveryPsc;
        $order->newsletter = 1;//$values->newsletter;
        $order->totalPrice = 0;
        $order->totalPriceVat = 0;
        if ($sessionPayment->payment == 2) {
            $order->state = 0;
        } else {
            $order->state = 1;
        }
        $order->createdAt = date("Y-m-d h:i:sa");
        $order->typePayment = $sessionPayment->payment;
        $this->orm->persistAndFlush($order);

        $totalPrice = 0;
        $totalPriceVat = 0;

        foreach ($sessionProducts as $item) {
            if($item){
                $product = $this->orm->products->getById($item['id']);
                $orderItems = new OrdersItem();
                $orderItems->name = $product->productName;
                $orderItems->price = $product->catalogPrice ? $product->catalogPrice : 0;
                $orderItems->priceVat = $product->catalogPriceVat;
                $orderItems->quantity = $item['quantity'];
                $orderItems->vat = 21;
                $orderItems->type = 1;
                $orderItems->product = $product;
                $orderItems->order = $order;
                $totalPrice += $product->catalogPrice*$item['quantity'];
                $totalPriceVat += $product->catalogPriceVat*$item['quantity'];
                try {
                    $this->orm->persistAndFlush($orderItems);
                } catch (\Exception $e) {
                    Debugger::log($e);
                }
            }
        }

        $orderItemsShipping = new OrdersItem();
        $orderItemsShipping->name = ($sessionShipping->shipping == 1?'Doručení na adresu přepravcem GEIS':'Osobní odběr na pobočce v Brně');
        $orderItemsShipping->price = ($sessionShipping->shipping == 1?'81':'0');
        $orderItemsShipping->priceVat = ($sessionShipping->shipping == 1?'98':'0');
        $orderItemsShipping->quantity = 1;
        $orderItemsShipping->vat = 21;
        $orderItemsShipping->type = 2;
        $orderItemsShipping->order = $order;
        $this->orm->persistAndFlush($orderItemsShipping);

        $orderItemsPayment = new OrdersItem();
        $orderItemsPayment->name = ($sessionPayment->payment == 1?'Dobírka GEIS':'Platba převodem na účet');
        $orderItemsPayment->price = ($sessionPayment->payment == 1?'25':'0');
        $orderItemsPayment->priceVat = ($sessionPayment->payment == 1?'30':'0');
        $orderItemsPayment->quantity = 1;
        $orderItemsPayment->vat = 21;
        $orderItemsPayment->type = 3;
        $orderItemsPayment->order = $order;
        $this->orm->persistAndFlush($orderItemsPayment);

        $totalPrice += $orderItemsShipping->price;
        $totalPriceVat += $orderItemsShipping->priceVat;
        $totalPrice += $orderItemsPayment->price;
        $totalPriceVat += $orderItemsPayment->priceVat;

        $order->totalPrice = $totalPrice;
        $order->totalPriceVat = $totalPriceVat;
        $this->orm->persistAndFlush($order);

        $sessionOrder->order = $order->id;

        //$this->invoiceService->createInvoice($order);

        $this->sendMails($order->id);

    }

    public function sendMails($id){
        $latte = new Engine();
        $orderEntity = $this->orm->orders->getById($id);
        /*
        $name = new Hi(new FileStorage(__DIR__ . '/../../AppModule/nameCache'));
        $name->setType(Hi::TYPE_NAME);
        $surname = new Hi(new FileStorage(__DIR__ . '/../../AppModule/nameCache'));
        $surname->setType(Hi::TYPE_SURNAME);
        $greetingsName = $name->to($orderEntity->name);
        $greetingsSurname = $surname->to($orderEntity->surname);
        if ($greetingsSurname->gender == $greetingsName->gender) {
            if ($greetingsSurname->gender == 'male') {
                $gender = 'pane';
            } else {
                $gender = 'paní';
            }
            $emailName = $greetingsSurname->vocativ;
        } else {
            $gender = "";
            $emailName = $greetingsName->vocativ;
        }
        */
        //$name = new Hi(new FileStorage(__DIR__ . '/../../AppModule/nameCache'));
        //$name->setType(Hi::TYPE_NAME);
       // $greetingsName = $name->to($orderEntity->name);
       // if ($greetingsName){
           // $emailName = $greetingsName->vocativ;
        //} else {
            $emailName = false;
        //}
        $hash = base64_encode($orderEntity->id).'8452';
        $order = [
            'id' => $orderEntity->id,
            'name' => $emailName,
            'ordersItems' => $orderEntity->ordersItems,
            'totalPriceVat' => $orderEntity->totalPriceVat,
            'basePath' => __DIR__,
            'hash' => $hash
        ];

        $mail = new Message();

        if ($orderEntity->typePayment == 2){
            $mail->setFrom(self::FROM_EMAIL)
                ->addTo($orderEntity->email)
                ->setSubject('Vaše objednávka č. ' . $order['id'] . ' | Animalko.cz - Veterinární a chovatelské vybavení')
                ->setHtmlBody($latte->renderToString(__DIR__ . '/../../BackModule/templates/Emails/orderSentBank.latte', $order))
                ->addEmbeddedFile(__DIR__ . '/../../../www/images/logo.png');
        } else {
            $mail->setFrom(self::FROM_EMAIL)
                ->addTo($orderEntity->email)
                ->setSubject('Vaše objednávka č. ' . $order['id'] . ' | Animalko.cz - Veterinární a chovatelské vybavení')
                ->setHtmlBody($latte->renderToString(__DIR__ . '/../../BackModule/templates/Emails/orderSent.latte', $order))
                ->addEmbeddedFile(__DIR__ . '/../../../www/images/logo.png');
        }
        $mailer = new SendmailMailer();
        $mailer->send($mail);
    }

    public function sentContactForm($values){
        $latte = new Engine();

        $subject = 'Nová zpráva z kontaktního formuláře!';

        $mail = new Message();

        $mail->setFrom(self::NO_REPLY_EMAIL)
            ->addTo(self::FROM_EMAIL)
            ->setSubject($subject)
            ->setHtmlBody($latte->renderToString(__DIR__ . '/../../BackModule/templates/Emails/newContact.latte', ['values' => $values, 'title' => $subject]), __DIR__."/../../../www/images/")
            ->addEmbeddedFile(__DIR__ . '/../../../www/images/logo-l.png');

        $mailer = new SendmailMailer();
        $mailer->send($mail);
    }

    public function sentServiceForm($values){
        $latte = new Engine();

        $subject = 'Zájem o servis nebo opravu!';

        $mail = new Message();

        $mail->setFrom(self::NO_REPLY_EMAIL)
            ->addTo(self::FROM_EMAIL)
            ->setSubject($subject)
            ->setHtmlBody($latte->renderToString(__DIR__ . '/../../BackModule/templates/Emails/newService.latte', ['values' => $values, 'title' => $subject]), __DIR__."/../../../www/images/")
            ->addEmbeddedFile(__DIR__ . '/../../../www/images/logo-l.png');

        $mailer = new SendmailMailer();
        $mailer->send($mail);
    }

    public function sentRedemptionForm($values){
        $latte = new Engine();

        $subject = 'Zájem o prodej auta!';

        $mail = new Message();

        $mail->setFrom(self::NO_REPLY_EMAIL)
            ->addTo(self::FROM_EMAIL)
            ->setSubject($subject)
            ->setHtmlBody($latte->renderToString(__DIR__ . '/../../BackModule/templates/Emails/newRedemption.latte', ['values' => $values, 'title' => $subject]), __DIR__."/../../../www/images/")
            ->addEmbeddedFile(__DIR__ . '/../../../www/images/logo-l.png');

        $mailer = new SendmailMailer();
        $mailer->send($mail);
    }
}