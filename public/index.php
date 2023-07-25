<?php
//todo: Только для dev.
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Source\Debug\InitCustomDumper;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

new InitCustomDumper();

$containerBuilder = new \DI\ContainerBuilder();

$container = $containerBuilder->build();

$app = AppFactory::createFromContainer($container);

//todo: Только для dev.
$app->addErrorMiddleware(true, false, false);

//----------------------------------------------------------------
// main routes
//----------------------------------------------------------------
$app->get('/', \App\Controllers\MainController::class . ':homepage');

//----------------------------------------------------------------
// test routes
//----------------------------------------------------------------
$app->get('/test/dump', \App\Controllers\TestControllers\MainTestController::class . ':testDump');
$app->get('/test/container', \App\Controllers\TestControllers\MainTestController::class . ':testContainer');

$app->run();