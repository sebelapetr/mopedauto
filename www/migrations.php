<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:08
 */

$container = require __DIR__ . '/../app/Bootstrap.migrations.php';

$debugIps = require __DIR__ . '/../debug-ips.php';

$conn = $container->getByType(\Nextras\Dbal\Connection::class);
$dbal = new \Nextras\Migrations\Bridges\NextrasDbal\NextrasAdapter($conn);
$driver = new Nextras\Migrations\Drivers\MySqlDriver($dbal);

$controllerClass = 'Nextras\\Migrations\\Controllers\\' . (PHP_SAPI === 'cli' ? 'Console' : 'Http') . 'Controller';
$controller = new $controllerClass($driver);
$controller->addGroup('structures', __DIR__ . '/../migrations/structures');
$controller->addGroup('basic-data', __DIR__ . '/../migrations/basic-data', ['structures']);
$controller->addGroup('dummy-data', __DIR__ . '/../migrations/dummy-data', ['basic-data']);
$controller->addExtension('sql', new Nextras\Migrations\Extensions\SqlHandler($driver));
$controller->run();