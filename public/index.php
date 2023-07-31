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

//todo Только для dev.
$container->set('app', $app);

//todo: Только для dev.
$app->addErrorMiddleware(true, false, false);

//----------------------------------------------------------------
// main routes
//----------------------------------------------------------------
$app->get('/', \App\Controllers\MainController::class . ':homepage');

$app->get('/quality/create', \App\Controllers\QualityController::class . ':create');
$app->get('/qualities', \App\Controllers\QualityController::class . ':all');
$app->get('/quality/update', \App\Controllers\QualityController::class . ':update');
$app->get('/quality/delete', \App\Controllers\QualityController::class . ':delete');

$app->get('/reference_product/create', \App\Controllers\ReferenceProductController::class . ':create');
$app->get('/reference_products', \App\Controllers\ReferenceProductController::class . ':all');
$app->get('/reference_product/update', \App\Controllers\ReferenceProductController::class . ':update');
$app->get('/reference_product/delete', \App\Controllers\ReferenceProductController::class . ':delete');

$app->get('/dish/create', \App\Controllers\DishController::class . ':create');
$app->get('/dishes', \App\Controllers\DishController::class . ':all');
$app->get('/dish/get', \App\Controllers\DishController::class . ':get');
$app->get('/dish/update', \App\Controllers\DishController::class . ':update');
$app->get('/dish/delete', \App\Controllers\DishController::class . ':delete');

$app->get('/dish_version/create', \App\Controllers\DishVersionController::class . ':create');
$app->get('/dish_versions', \App\Controllers\DishVersionController::class . ':all');
$app->get('/dish_version/get', \App\Controllers\DishVersionController::class . ':get');
$app->get('/dish_version/update', \App\Controllers\DishVersionController::class . ':update');
$app->get('/dish_version/delete', \App\Controllers\DishVersionController::class . ':delete');

$app->get('/recipe/create', \App\Controllers\RecipeController::class . ':create');
$app->get('/recipe/get', \App\Controllers\RecipeController::class . ':get');
$app->get('/recipe/add_product', \App\Controllers\RecipeController::class . ':addProduct');
$app->get('/recipe/remove_product', \App\Controllers\RecipeController::class . ':removeProduct');
$app->get('/recipe/commit', \App\Controllers\RecipeController::class . ':commit');
//$app->get('/recipe/create_branch', \App\Controllers\RecipeController::class . ':createBranch');

//----------------------------------------------------------------
// sandbox routes
//----------------------------------------------------------------
$app->get('/sandbox', \App\Controllers\SandboxControllers\MainSandboxController::class . ':main');
$app->get('/sandbox/db', \App\Controllers\SandboxControllers\DatabaseSandboxController::class . ':run');
$app->get('/sandbox/data_manager', \App\Controllers\SandboxControllers\DataManagerSandbox::class . ':run');
$app->get('/sandbox/api', \App\Controllers\SandboxControllers\ApiSandboxController::class . ':run');
$app->get('/sandbox/validation', \App\Controllers\SandboxControllers\ValidationSandboxController::class . ':run');

//----------------------------------------------------------------
// test routes
//----------------------------------------------------------------
$app->get('/test', \App\Controllers\TestControllers\MainTestController::class . ':main');
$app->get('/test/dump', \App\Controllers\TestControllers\MainTestController::class . ':testDump');
$app->get('/test/container', \App\Controllers\TestControllers\MainTestController::class . ':testContainer');

$app->run();
