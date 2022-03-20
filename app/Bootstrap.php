<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 22:08
 */

declare(strict_types=1);

namespace App;

use Nette\Configurator;
use Tracy\Debugger;


class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;

        define("WWW_DIR", __DIR__.'/../www');
        define("APP_DIR", __DIR__);
        define("TEMP_DIR", __DIR__.'/../temp');

        $debugIps = require __DIR__ . '/../debug-ips.php';
        $configurator->setDebugMode($debugIps);
		$configurator->enableTracy(__DIR__ . '/../log');
        Debugger::$errorTemplate = __DIR__.'/presenters/Error/500.latte';


        $configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/../temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addConfig(__DIR__ . '/config/common.neon');
		$configurator->addConfig(__DIR__ . '/config/local.neon');

		return $configurator;
	}
}
