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
        $routerFront[] = new Route('kosik/osobni-udaje', 'Cart:step1');
        $routerFront[] = new Route('<presenter>/<action>', 'Homepage:default');

		return $router;
	}
}
