<?php

namespace App\Model;

use Curl\Curl;
use Nette\Security\Passwords;
use Nette\Utils\FileSystem;
use Tracy\Debugger;
use Nette\SmartObject;

class InvoiceService
{

    /** @var Orm */
    private $orm;

    public $apiKey;

    public $email;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
        $this->apiKey = 'moZ4a*4737@3607*XunLqw9J!0np2CgL';
        $this->email = 'petrsebel@seznam.cz';
    }

    public function createInvoice(Order $order)
    {
        $testMode = 0;
        $invoice = $this->getInvoice($order);
        $invoiceItems = $this->getInvoiceItems($order);
        $supplier = $this->getSupplier();
        $subscriber = $this->getSubscriber($order);

        $data = [
            'key' => $this->apiKey,
            'email' => $this->email,
            'apitest' => $testMode,
            'd' => $supplier,
            'o' => $subscriber,
            'f' => $invoice,
            'p' => $invoiceItems
        ];

        $data_json = json_encode($data);

        $url = 'https://www.fakturyweb.cz/api/nf?data=' . urlencode($data_json);

        $ch = new Curl();
        $ch->setOpt(CURLOPT_URL, $url);
        $ch->setOpt(CURLOPT_COOKIESESSION, true);
        $ch->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $ch->setOpt(CURLOPT_CONNECTTIMEOUT, 5);
        $ch->get($url);
        $result = json_decode($ch->response, true);

        if($result['status'] == 1)
        {
            //$this->showInvoice($result['code'], $order);
        } else {
            Debugger::barDump($result['status']);
        }
    }

    public function createSession()
    {
        $data = [
          'key' => $this->apiKey,
          'email' => $this->email
        ];

        $data_json = json_encode($data);

        $url = 'https://www.fakturyweb.cz/api/init?data=' . urlencode($data_json);

        $ch = new Curl();
        $ch->setOpt(CURLOPT_URL, $url);
        $ch->setOpt(CURLOPT_COOKIESESSION, true);
        $ch->setOpt(CURLOPT_COOKIEJAR, __DIR__.'/cockie.txt');
        $ch->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $ch->setOpt(CURLOPT_CONNECTTIMEOUT, 5);
        $ch->get($url);
        $result = json_decode($ch->response, true);

        if($result['status'] == 1)
        {
            Debugger::barDump($result);
            return $ch;
        } else {
            Debugger::barDump($result['status']);
        }
    }

    public function showInvoice($code, $order)
    {

        $ch = $this->createSession();

        $data = [
            'download' => 'download',
            'code' => $code,
            'email' => $this->email,
            'key' => $this->apiKey,
        ];

        Debugger::barDump($data);
        $data_json = json_encode($data);

        $url = 'https://www.fakturyweb.cz/api/zf?data=' . urlencode($data_json);

        $ch->setOpt(CURLOPT_URL, $url);
        $ch->get($url);
        $result = json_decode($ch->response, true);
        Debugger::barDump($result);

        if($result['status'] == 1)
        {
            Debugger::barDump($result);
            header('Content-Type: application/download');
            header('Content-Disposition: inline; filename="'.$order->id.'.pdf"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            $ch->setOpt(CURLOPT_URL, $url);
            $ch->setOpt(CURLOPT_FOLLOWLOCATION, true);
            $ch->get($url);
            $result = json_decode($ch->response, true);
            //$this->downloadInvoice($result['url'],$order);
        } else {
            Debugger::barDump($result['status']);
        }


    }

    public function downloadInvoice($url, Order $order)
    {
        $this->orm->persistAndFlush($order);
        $file = $url;
        $downloadPath = __DIR__ . '/../invoice/'.$order->id.'.pdf';
    }

    public function getSupplier(){
        $supplier = [
            'd_id' => 5187
        ];
        return $supplier;
    }

    public function getSubscriber(Order $order)
    {
        $subscriber = [
          'o_name' => $order->name.' '.$order->surname,
          'o_street' => $order->street,
          'o_city' => $order->city,
          'o_zip' => $order->psc,
          'o_state' => 'Česká republika',
          'o_ico' => $order->ico,
          'o_dic' => $order->dic,
          'o_email' => $order->email,
          'o_name_d' => $order->deliveryName.' '.$order->deliverySurname,
          'o_street_d' => $order->deliveryStreet,
          'o_city_d' => $order->deliveryCity,
          'o_state_d' => 'Česká republika',
          'o_zip_d' => $order->deliveryPsc
        ];

        return $subscriber;
    }

    public function getInvoiceItems(Order $order)
    {
        foreach ($order->ordersItems as $item){
            $invoiceItem = [
              'p_text' => html_entity_decode($item->name),
              'p_quantity' => $item->quantity,
              'p_unit' => 'ks',
              //'p_price' => $item->price, Nejsem platce dph a do faktur se zapisuje bez dph
              'p_price' => $item->priceVat,
              'p_vat' => $item->vat,
              'p_priceVat' => $item->priceVat
            ];
            $invoiceItems[] = $invoiceItem;
        }

        return $invoiceItems;
    }

    public function getInvoice(Order $order)
    {
        $invoice = [
          'f_number' => $order->id,
          'f_vs' => $order->variableSymbol,
          //'f_ks' => ,
          //'f_date_issue' => ,
          //'f_date_delivery' => ,
          //'f_date_due' => ,
          'f_issued_by' => 'Petr Šebela',
          'f_payment' => $this->getPayment($order),
          'f_logo' => 1,
          'f_stamp' => 1,
          'f_currency' => 'kč',
          'f_proforma' => 0,
          'f_paid' => 0,
          'f_style' => 'standard',
          'f_language' => 'CZ',
          'f_qr' => 0,
          'f_order' => $order->id,
          'f_note' => $order->note,
          'f_omit_stats' => 0
        ];

        return $invoice;
    }

    public function getPayment($order){
        $typePayment = $this->orm->ordersItems->getBy(['order'=>$order, 'type'=>3]);

        switch ($typePayment->name) {
            case 'Dobírka GEIS':
                $payment = 'dobirka';
                break;

            case 'Platba převodem na účet':
                $payment = 'prevod';
                break;
        }

        return $payment;
    }

    public function sendEmails(){

    }

}