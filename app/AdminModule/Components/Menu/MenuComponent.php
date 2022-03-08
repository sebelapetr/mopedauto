<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:48
 */

declare(strict_types=1);

namespace App\AdminModule\Components;

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
                    'icon' => 'fas fa-shopping-cart'
                ],
                [
                    'presenter' => 'Products',
                    'presenterClean' => StringUtils::clean('Products'),
                    'icon' => 'fas fa-barcode',
                ],
                [
                    'presenter' => 'Categories',
                    'presenterClean' => StringUtils::clean('Categories'),
                    'icon' => 'fas fa-bars',
                ],
                [
                    'presenter' => 'Contacts',
                    'presenterClean' => StringUtils::clean('Contacts'),
                    'icon' => 'fas fa-address-book'
                ],/*
                [
                    'presenter' => 'Documents',
                    'presenterClean' => StringUtils::clean('Documents'),
                    'icon' => 'fas fa-file-contract',
                ],*/
                [
                    'presenter' => 'Users',
                    'presenterClean' => StringUtils::clean('Users'),
                    'icon' => 'fas fa-users',
                    'children' => [
                        [
                            'presenter' => 'Users',
                            'presenterClean' => StringUtils::clean('Users'),
                            'icon' => 'fas fa-users',
                        ],
                        [
                            'presenter' => 'Rights',
                            'presenterClean' => StringUtils::clean('Rights'),
                            'icon' => 'fas fa-ban',
                        ],
                    ],
                ],/*
                [
                    'presenter' => 'SystemSettings',
                    'presenterClean' => StringUtils::clean('SystemSettings'),
                    'icon' => 'fas fa-cog',
                ],*/
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