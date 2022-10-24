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
 * @property-read SettingsRepository $settings
 * @property-read RolesRepository $roles
 * @property-read ActionsRepository $actions
 * @property-read RightsRepository $rights
 * @property-read ProductParametersRepository $productParameters
 * @property-read ProductParameterValuesRepository $productParameterValues
 * @property-read VehiclesRepository $vehicles
 * @property-read VehicleImagesRepository $vehicleImages
 * @property-read CustomersRepository $customers
 */
class Orm extends Model
{
}