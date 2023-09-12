<?php
// @only_dev
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use App\Controller\UserController;
use App\Controller\DishController;
use App\Controller\DishVersionController;
use App\Controller\MainController;
use App\Controller\QualityController;
use App\Controller\RecipeController;
use App\Controller\ReferenceProductController;
use App\Controller\Sandbox\ApiSandboxController;
use App\Controller\Sandbox\DatabaseSandboxController;
use App\Controller\Sandbox\DataManagerSandbox;
use App\Controller\Sandbox\MainSandboxController;
use App\Controller\Sandbox\MiddlewareSandboxController;
use App\Controller\Sandbox\ValidationSandboxController;
use App\Controller\Security\AuthController;
use App\Controller\Security\RegisterController;
use App\Controller\Test\MainTestController;
use App\Debug\InitCustomDumper;
use App\Middleware\ExampleDishesMiddleware;
use App\Middleware\OnlyAdminMiddleware;
use App\Middleware\OnlyAuthMiddleware;
use App\Middleware\OnlyDevMiddleware;
use App\Middleware\TryLoginByApiKeyMiddleware;
use App\Service\ResponseBuilder;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteCollectorProxyInterface;

require __DIR__ . '/../vendor/autoload.php';

new InitCustomDumper();

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAttributes(true);
$containerBuilder->addDefinitions(__DIR__ . '/../app/container.php');

$container = $containerBuilder->build();

$app = AppFactory::createFromContainer($container);

// @only_dev
$container->set('app', $app);

$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails,
    ?LoggerInterface $logger = null
) use ($app, $container) {
    if ($logger) $logger->error($exception->getMessage());

    $responseBuilder = $container->get(ResponseBuilder::class);
    $responseBuilder->addError($exception->getMessage());   //todo: Не удобно. Нужно больше информации.

    $response = $app->getResponseFactory()->createResponse();

    return $responseBuilder->build($response);
};

// @only_dev
$errorMiddleware = $app->addErrorMiddleware(true, false, false);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

//----------------------------------------------------------------
// main routes
//----------------------------------------------------------------
$app->get('/', [MainController::class, 'homepage']);

$app->group('', function (RouteCollectorProxyInterface $group) {
    $group->get('/register', [RegisterController::class, 'register']);
})
    ->addMiddleware($container->get(ExampleDishesMiddleware::class))    //todo: Нужна настройка для включения/отключения.
;
//$app->get('/register', [RegisterController::class, 'register']);
$app->get('/generate_api_key', [AuthController::class, 'generateApiKey']);

$app->group('', function (RouteCollectorProxyInterface $group) {
    $group->get('/user', [UserController::class, 'info']);
})
    ->addMiddleware($container->get(OnlyAuthMiddleware::class))
    ->addMiddleware($container->get(TryLoginByApiKeyMiddleware::class))
;

$app->group('', function (RouteCollectorProxyInterface $group) {
    $group->get('/quality/create', [QualityController::class, 'create']);
    $group->get('/quality/update', [QualityController::class, 'update']);
    $group->get('/quality/delete', [QualityController::class, 'delete']);

    $group->get('/reference_product/create', [ReferenceProductController::class, 'create']);
    $group->get('/reference_product/update', [ReferenceProductController::class, 'update']);
    $group->get('/reference_product/delete', [ReferenceProductController::class, 'delete']);
})
    ->addMiddleware($container->get(OnlyAdminMiddleware::class))
    ->addMiddleware($container->get(OnlyAuthMiddleware::class))
    ->addMiddleware($container->get(TryLoginByApiKeyMiddleware::class))
;

$app->group('', function (RouteCollectorProxyInterface $group) {
    $group->get('/qualities', [QualityController::class, 'all']);
    $group->get('/quality/get', [QualityController::class, 'get']);

    $group->get('/reference_products', [ReferenceProductController::class, 'all']);
    $group->get('/reference_product/get', [ReferenceProductController::class, 'get']);
})
    ->addMiddleware($container->get(OnlyAuthMiddleware::class))
    ->addMiddleware($container->get(TryLoginByApiKeyMiddleware::class))
;

$app->group('', function (RouteCollectorProxyInterface $group) {
    $group->get('/dish/create', [DishController::class, 'create']);
    $group->get('/dishes', [DishController::class, 'all']);
    $group->get('/dish/get', [DishController::class, 'get']);
    $group->get('/dish/update', [DishController::class, 'update']);
    $group->get('/dish/delete', [DishController::class, 'delete']);
})
    ->addMiddleware($container->get(OnlyAuthMiddleware::class))
    ->addMiddleware($container->get(TryLoginByApiKeyMiddleware::class))
;

$app->group('', function (RouteCollectorProxyInterface $group) {
    $group->get('/dish_version/create', [DishVersionController::class, 'create']);
    $group->get('/dish_versions', [DishVersionController::class, 'all']);
    $group->get('/dish_version/get', [DishVersionController::class, 'get']);
    $group->get('/dish_version/update', [DishVersionController::class, 'update']);
    $group->get('/dish_version/delete', [DishVersionController::class, 'delete']);
})
    ->addMiddleware($container->get(OnlyAuthMiddleware::class))
    ->addMiddleware($container->get(TryLoginByApiKeyMiddleware::class))
;

$app->group('', function (RouteCollectorProxyInterface $group) {
    $group->get('/recipe/create', [RecipeController::class, 'create']);   //todo: Нет ui.
    $group->get('/recipes', [RecipeController::class, 'all']);
    $group->get('/recipe/get', [RecipeController::class, 'get']);
    $group->get('/recipe/add_product', [RecipeController::class, 'addProduct']);
    $group->get('/recipe/remove_product', [RecipeController::class, 'removeProduct']);
    $group->get('/recipe/commit', [RecipeController::class, 'commit']);
    $group->get('/recipe/branch', [RecipeController::class, 'branch']);
})
    ->addMiddleware($container->get(OnlyAuthMiddleware::class))
    ->addMiddleware($container->get(TryLoginByApiKeyMiddleware::class))
;

//----------------------------------------------------------------
// sandbox routes
// @only_dev todo: Сделать так, чтобы кода не было физически не на dev.
//----------------------------------------------------------------
$app->group('', function (RouteCollectorProxyInterface $group) use ($container) {
    $group->get('/sandbox', [MainSandboxController::class, 'main']);
    $group->get('/sandbox/db', [DatabaseSandboxController::class, 'run']);
    $group->get('/sandbox/data_manager', [DataManagerSandbox::class, 'run']);
    $group->get('/sandbox/api', [ApiSandboxController::class, 'run']);
    $group->get('/sandbox/validation', [ValidationSandboxController::class, 'run']);
    $group->get('/sandbox/middleware/login', [MiddlewareSandboxController::class, 'loginByApiKey'])
        ->addMiddleware($container->get(OnlyAuthMiddleware::class))
        ->addMiddleware($container->get(TryLoginByApiKeyMiddleware::class))
    ;
})
    ->addMiddleware($container->get(OnlyDevMiddleware::class))
;

//----------------------------------------------------------------
// test routes
// @only_dev
//----------------------------------------------------------------
$app->group('', function (RouteCollectorProxyInterface $group) use ($container) {
    $group->get('/test', [MainTestController::class, 'main']);
    $group->get('/test/dump', [MainTestController::class, 'testDump']);
    $group->get('/test/container', [MainTestController::class, 'testContainer']);
})
    ->addMiddleware($container->get(OnlyDevMiddleware::class))
;

$app->run();
