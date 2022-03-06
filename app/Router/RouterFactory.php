<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

        $router[] = $routerApp = new RouteList("Back");
        $routerApp[] = new Route('admin/<presenter>/<action>[/<id>]', 'Dashboard:default');

        $router[] = $routerFront = new RouteList("Front");
        $routerFront[] = new Route('nabidka-mopedaut', 'Cars:default');
        $routerFront[] = new Route('nahradni-dily[/<seoName>]', 'SpareParts:default');
        $routerFront[] = new Route('nahradni-dily/produkt/<seoName>', 'SpareParts:detail');
        $routerFront[] = new Route('kosik/osobni-udaje', 'Cart:step1');
        $routerFront[] = new Route('kosik/doprava-a-platba', 'Cart:step2');
        $routerFront[] = new Route('kosik/dokoncena-objednavka/<hash>', 'Cart:step3');
        $routerFront[] = new Route('kosik/prazdny-kosik', 'Cart:empty');
        $routerFront[] = new Route('vykup', 'Pages:redemption');
        $routerFront[] = new Route('servis', 'Pages:service');
        $routerFront[] = new Route('o-mopedautech', 'Pages:about');
        $routerFront[] = new Route('kontakt', 'Pages:contact');
        $routerFront[] = new Route('<presenter>/<action>', 'Homepage:default');

		return $router;
	}
}
