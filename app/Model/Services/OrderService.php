<?php

namespace App\Model;

use App\Model\Services\ComgateService;
use Latte\Engine;
use Nette\Application\LinkGenerator;
use Nette\Caching\Storages\FileStorage;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Nextras\Dbal\Utils\DateTimeImmutable;
use ondrs\Hi\Hi;
use Tracy\Debugger;

class OrderService
{

    /** @var Orm */
    private $orm;

    /** @var InvoiceService */
    public $invoiceService;

    /** @var LinkGenerator */
    public $linkGenerator;

    public ComgateService $comgateService;

    public const FROM_EMAIL = 'mopedauto.cz <info@mopedauto.cz>';
    public const NO_REPLY_EMAIL = 'mopedauto.cz <noreply@mopedauto.cz>';

    public function __construct(Orm $orm, InvoiceService $invoiceService, LinkGenerator $linkGenerator, ComgateService $comgateService)
    {
        $this->orm = $orm;
        $this->invoiceService = $invoiceService;
        $this->linkGenerator = $linkGenerator;
        $this->comgateService = $comgateService;
    }

    public function saveOrder(Order $order, $values)
    {
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

        if ($values->otherAddress == true) {
            $order->deliveryName = $values->deliveryName;
            $order->deliverySurname = $values->deliverySurname;
            $order->deliveryCompany = $values->deliveryCompany;
            $order->deliveryStreet = $values->deliveryStreet;
            $order->deliveryCity = $values->deliveryCity;
            $order->deliveryPsc = $values->deliveryPsc;
        } else {
            $order->deliveryName = $order->name;
            $order->deliverySurname = $order->surname;
            $order->deliveryCompany = $order->company;
            $order->deliveryStreet = $order->street;
            $order->deliveryCity = $order->city;
            $order->deliveryPsc = $order->psc;
        }
        $order->createdAt = new DateTimeImmutable();

        $this->orm->persistAndFlush($order);

        return $order;
    }

    public function sendMails(Order $order){
        $latte = new Engine();
        $hash = base64_encode($order->id).'8452';

        $mail = new Message();

        $title = 'Vaše objednávka č. ' . $order->id . ' | mopedauto.cz';
        $mail->setFrom(self::FROM_EMAIL)
            ->addTo($order->email)
            ->addBcc(self::FROM_EMAIL)
            ->setSubject($title)
            ->setHtmlBody($latte->renderToString(__DIR__ . '/../../AdminModule/templates/Emails/orderSent.latte', [
                'order' => $order,
                'basePath' => __DIR__,
                'hash' => $hash,
                'orderDetailUrl' => $this->linkGenerator->link('Front:Cart:step3', ['hash' => $hash]),
                'title' => $title
            ]))
            ->addEmbeddedFile(__DIR__ . '/../../../www/images/logo.png');

        $mailer = new SendmailMailer();
        $mailer->send($mail);

        return true;
    }

    public function sentContactForm($values){
        $latte = new Engine();

        $subject = 'Nová zpráva z kontaktního formuláře!';

        $mail = new Message();

        $mail->setFrom(self::NO_REPLY_EMAIL)
            ->addTo(self::FROM_EMAIL)
            ->setSubject($subject)
            ->setHtmlBody($latte->renderToString(__DIR__ . '/../../AdminModule/templates/Emails/newContact.latte', ['values' => $values, 'title' => $subject]), __DIR__."/../../../www/images/")
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
            ->setHtmlBody($latte->renderToString(__DIR__ . '/../../AdminModule/templates/Emails/newService.latte', ['values' => $values, 'title' => $subject]), __DIR__."/../../../www/images/")
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
            ->setHtmlBody($latte->renderToString(__DIR__ . '/../../AdminModule/templates/Emails/newRedemption.latte', ['values' => $values, 'title' => $subject]), __DIR__."/../../../www/images/")
            ->addEmbeddedFile(__DIR__ . '/../../../www/images/logo-l.png');

        $mailer = new SendmailMailer();
        $mailer->send($mail);
    }
}