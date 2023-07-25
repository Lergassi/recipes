<?php
//todo: Только для dev.
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use App\Debug\InitCustomDumper;
use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

new InitCustomDumper();

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions([
    PDO::class => function (ContainerInterface $container) {
        return new \PDO(
            sprintf('mysql:host=%s;dbname=%s', $_ENV['APP_DB_HOST'] ?? '', $_ENV['APP_DB_NAME'] ?? ''),
            $_ENV['APP_DB_USER'] ?? '',
            $_ENV['APP_DB_PASSWORD'] ?? '',
            [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
            ]
        );
    },
]);

$container = $containerBuilder->build();

$app = AppFactory::createFromContainer($container);

//todo: Только для dev.
$app->addErrorMiddleware(true, false, false);

//----------------------------------------------------------------
// main routes
//----------------------------------------------------------------
$app->get('/', \App\Controllers\MainController::class . ':homepage');

//----------------------------------------------------------------
// sandbox routes
//----------------------------------------------------------------
$app->get('/sandbox', \App\Controllers\SandboxControllers\MainSandboxController::class . ':main');

//----------------------------------------------------------------
// test routes
//----------------------------------------------------------------
$app->get('/test', \App\Controllers\TestControllers\MainTestController::class . ':main');
$app->get('/test/dump', \App\Controllers\TestControllers\MainTestController::class . ':testDump');
$app->get('/test/container', \App\Controllers\TestControllers\MainTestController::class . ':testContainer');

$app->run();