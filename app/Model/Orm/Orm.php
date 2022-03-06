<?php
namespace App\Model;
use Nextras\Orm\Model\Model;
/**
 * Model
 * @property-read CategoriesRepository $categories
 * @property-read ProductsRepository $products
 * @property-read ProductCategoriesRepository $productCategories
 * @property-read ProductImagesRepository $productImages
 * @property-read UsersRepository $users
 * @property-read QuotesRepository $quotes
 * @property-read OrdersRepository $orders
 * @property-read OrdersItemsRepository $ordersItems
 * @property-read NewslettersRepository $newsletters
 * @property-read ComgatePaymentsRepository $comgatePayments
 */
class Orm extends Model
{
}