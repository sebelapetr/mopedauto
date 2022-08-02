<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Forms\IContactFormFactory;
use App\Model\Order;
use App\Model\OrdersItem;
use App\Model\Orm;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IComponent;
use Nette\Utils\Random;
use Nextras\Dbal\Connection;
use Nextras\Dbal\Utils\DateTimeImmutable;
use Tracy\Debugger;

Class ImportsPresenter extends Presenter
{

    public Orm $orm;
    public Connection $connection;

    public function __construct(Orm $orm, Connection $connection)
    {
        $this->orm = $orm;
        $this->connection = $connection;
    }

    public function renderParams(){

        $row = 1;
        if (($handle = fopen(WWW_DIR . "/param.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
                $row++;

                $productId = $data[1];
                $paramId = $data[2];
                echo $productId . " - " . $paramId . "<br>";
            }
            fclose($handle);
        }


        $this->getTemplate()->setFile(__DIR__ . "/../templates/Imports/default.latte");
    }

    public function renderOrders(){

        $row = 0;
        if (($handle = fopen("ssss.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $row++;
                $id = $data[0];
                $state = $this->getState($data[1]);
                $date = $data[2];
                $firstName = $data[3];
                $secondName = $data[4];
                $email = $data[5];
                $phone = $data[6];
                $street = $data[7];
                $city = $data[8];
                $zip = $data[9];
                $company = $data[10];
                $ico = $data[11];
                $dic = $data[12];
                $deliveryName = $data[13];
                $deliverySurname = $data[14];
                $deliveryCompany = $data[15];
                $deliveryStreet = $data[16];
                $deliveryCity = $data[17];
                $deliveryZip = $data[18];
                $note = $data[19]; //dodelat
                $price = floatval($data[20]) / 1.21;
                $priceVat = floatval($data[20]);
                $userId = $data[23]; //dodelat

                $order = new Order();
                $order->id = $id;
                $order->state = $state;
                $order->hash = Random::generate(20);
                $order->name = $firstName;
                $order->surname = $secondName;
                $order->telephone = $phone;
                $order->email = $email;
                $order->street = $street;
                $order->city = $city;
                $order->psc = $zip;
                $order->note = $note;
                $order->company = $company;
                $order->ico = $ico;
                $order->dic = $dic;
                $order->deliveryName = $deliveryName;
                $order->deliverySurname = $deliverySurname;
                $order->deliveryCompany = $deliveryCompany;
                $order->deliveryStreet = $deliveryStreet;
                $order->deliveryCity = $deliveryCity;
                $order->deliveryPsc = $deliveryZip;
                $order->totalPrice = $price;
                $order->totalPriceVat = $priceVat;
                $order->userId = $userId;
                Debugger::barDump($date);
                $dateForm = DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $date);
                Debugger::barDump($dateForm);
                $order->sentAt = $dateForm;
                $order->createdAt = $dateForm;
                $this->orm->persistAndFlush($order);
            }

            fclose($handle);
        }


        $this->getTemplate()->setFile(__DIR__ . "/../templates/Imports/default.latte");
    }

    public function renderOrderItems(){

        $row = 0;
        if (($handle = fopen("rrs.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $row++;

                $orderId = $data[1];
                $name = $data[3];
                $price = floatval($data[4]);
                $quantity = intval($data[5]);
                $productId = $data[6] !== '0' ? $data[6] : null;

                if ($productId) {


                    $this->connection->query("
                    INSERT IGNORE INTO `orders_items` (`type`, `name`, `price_piece`, `price_piece_Vat`, `price`, `price_vat`, `quantity`, `vat`, `product`, `order`, `created_at`, `category_type`)
VALUES ('".OrdersItem::TYPE_PRODUCT."', '$name', '".($price / 1.21)."', '$price', '".($price / 1.21) * $quantity."', '".$price * $quantity."', '$quantity', '21', $productId, '$orderId', now(), NULL);
              
                ");
                } else {

                    $this->connection->query("
                    INSERT IGNORE INTO `orders_items` (`type`, `name`, `price_piece`, `price_piece_Vat`, `price`, `price_vat`, `quantity`, `vat`, `product`, `order`, `created_at`, `category_type`)
VALUES ('".OrdersItem::TYPE_PRODUCT."', '$name', '".($price / 1.21)."', '$price', '".($price / 1.21) * $quantity."', '".$price * $quantity."', '$quantity', '21', NULL, '$orderId', now(), NULL);
              
                ");
                }
            }

            fclose($handle);
        }


        $this->getTemplate()->setFile(__DIR__ . "/../templates/Imports/default.latte");
    }

    private function getState($stateId)
    {
        switch ($stateId) {
            case 1:
                return Order::ORDER_STATE_RECEIVED;
            break;
            case 2:
                return Order::ORDER_STATE_COMPLETE;
                break;
            case 3:
                return Order::ORDER_STATE_COMPLETE;
                break;
            case 4:
                return Order::ORDER_STATE_STORNO;
                break;

        }
    }
}