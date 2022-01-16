<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:48
 */

declare(strict_types=1);

namespace App\AppModule\Components;

use App\Components\BaseComponent;
use App\Model\Utils\StringUtils;

interface IMenuComponentFactory
{
	function create(): MenuComponent;
}

class MenuComponent extends BaseComponent {
	
	public function render(): void
	{
		$this->getTemplate()->menuItems =
			[
                [
                    'presenter' => 'Dashboard',
                    'presenterClean' => StringUtils::clean('Dashboard'),
                    'icon' => 'fas fa-fw fa-tachometer-alt',
                ],
                [
                    'presenter' => 'Orders',
                    'presenterClean' => StringUtils::clean('Orders'),
                    'icon' => 'fas fa-shopping-cart',
                ],
                [
                    'presenter' => 'Partners',
                    'presenterClean' => StringUtils::clean('Partners'),
                    'icon' => 'fas fa-handshake',
                    'children' => [
                        [
                            'presenter' => 'Partners',
                            'presenterClean' => StringUtils::clean('Partners')
                        ],
                        [
                            'presenter' => 'Branches',
                            'presenterClean' => StringUtils::clean('Branches')
                        ]
                    ]
                ],
                [
                    'presenter' => 'Couriers',
                    'presenterClean' => StringUtils::clean('Couriers'),
                    'icon' => 'fas fa-people-carry',
                ],
                [
                    'presenter' => 'Operators',
                    'presenterClean' => StringUtils::clean('Operators'),
                    'icon' => 'fas fa-phone-volume',
                ],
                [
                    'presenter' => 'Vehicles',
                    'presenterClean' => StringUtils::clean('Vehicles'),
                    'icon' => 'fas fa-truck',
                ],
                [
                    'presenter' => 'DeliveryTypes',
                    'presenterClean'=> StringUtils::clean('DeliveryTypes'),
                    'icon'=>'fas fa-shipping-fast',
                ],
                [
                    'presenter' => 'Documents',
                    'presenterClean' => StringUtils::clean('Documents'),
                    'icon' => 'fas fa-file-contract',
                ],
                [
                    'presenter' => 'Support',
                    'presenterClean' => StringUtils::clean('Support'),
                    'icon' => 'fas fa-question-circle',
                ],
                [
                    'presenter' => 'Cities',
                    'presenterClean' => StringUtils::clean('Cities'),
                    'icon' => 'fas fa-city',
                    'children' => [
                        [
                            'presenter' => 'Cities',
                            'presenterClean' => StringUtils::clean('Cities'),
                            'icon' => 'fas fa-city',
                        ],
                        [
                            'presenter' => 'Zips',
                            'presenterClean' => StringUtils::clean('Zips'),
                            'icon' => 'fas fa-city',
                        ],
                    ]
                ],
                [
                    'presenter' => 'Cms',
                    'presenterClean' => StringUtils::clean('Cms'),
                    'icon' => 'fas fa-columns',
                ],
                [
                    'presenter' => 'Users',
                    'presenterClean' => StringUtils::clean('Users'),
                    'icon' => 'fas fa-users',
                ],
                [
                    'presenter' => 'SystemSettings',
                    'presenterClean' => StringUtils::clean('SystemSettings'),
                    'icon' => 'fas fa-cog',
                ],
                [
                    'presenter' => 'PaymentTypes',
                    'presenterClean'=> StringUtils::clean('PaymentTypes'),
                    'icon'=>'fas fa-money-bill-alt',
                ]
			];
		$this->setTemplateFile();
		$this->getTemplate()->render();
	}

    public function getLinksRecursiveString(array $itemData): string
    {
        $links = $this->getLinksRecursive($itemData);
        return implode('|', $links);
    }

    protected function getLinksRecursive(array $itemData, array $links = []): array
    {
        $links[] = $itemData['presenter'] . ':*';
        if(isset($itemData['children'])){
            foreach($itemData['children'] as $childData){
                $links = $this->getLinksRecursive($childData, $links);
            }
        }
        return $links;
    }

}