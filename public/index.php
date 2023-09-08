<?php
//todo: Только для dev.
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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
use App\Middleware\OnlyAuthMiddleware;
use App\Middleware\TryLoginByApiKeyMiddleware;
use App\Service\ResponseBuilder;
use App\Service\Validation\UniqueConstraint;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use function DI\autowire;
use function DI\get;

require __DIR__ . '/../vendor/autoload.php';

new InitCustomDumper();

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAttributes(true);
$containerBuilder->addDefinitions(__DIR__ . '/../app/container.php');

$container = $containerBuilder->build();

$app = AppFactory::createFromContainer($container);

//todo Только для dev.
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
    $responseBuilder->addError($exception->getMessage());

    $response = $app->getResponseFactory()->createResponse();

    return $responseBuilder->build($response);
};

//todo: Только для dev.
$errorMiddleware = $app->addErrorMiddleware(true, false, false);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

//----------------------------------------------------------------
// main routes
//----------------------------------------------------------------
$app->get('/', [MainController::class, 'homepage']);

$app->get('/register', [RegisterController::class, 'register']);
$app->get('/generate_api_key', [AuthController::class, 'generateApiKey']);

$app->get('/quality/create', [QualityController::class, 'create']);
$app->get('/qualities', [QualityController::class, 'all']);
$app->get('/quality/get', [QualityController::class, 'get']);
$app->get('/quality/update', [QualityController::class, 'update']);
$app->get('/quality/delete', [QualityController::class, 'delete']);

$app->get('/reference_product/create', [ReferenceProductController::class, 'create']);
$app->get('/reference_products', [ReferenceProductController::class, 'all']);
$app->get('/reference_product/get', [ReferenceProductController::class, 'get']);
$app->get('/reference_product/update', [ReferenceProductController::class, 'update']);
$app->get('/reference_product/delete', [ReferenceProductController::class, 'delete']);

$app->get('/dish/create', [DishController::class, 'create']);
$app->get('/dishes', [DishController::class, 'all']);
$app->get('/dish/get', [DishController::class, 'get']);
$app->get('/dish/update', [DishController::class, 'update']);
$app->get('/dish/delete', [DishController::class, 'delete']);

$app->get('/dish_version/create', [DishVersionController::class, 'create']);
$app->get('/dish_versions', [DishVersionController::class, 'all']);
$app->get('/dish_version/get', [DishVersionController::class, 'get']);
$app->get('/dish_version/update', [DishVersionController::class, 'update']);
$app->get('/dish_version/delete', [DishVersionController::class, 'delete']);

$app->get('/recipe/create', [RecipeController::class, 'create']);
$app->get('/recipes', [RecipeController::class, 'all']);
$app->get('/recipe/get', [RecipeController::class, 'get']);
$app->get('/recipe/add_product', [RecipeController::class, 'addProduct']);
$app->get('/recipe/remove_product', [RecipeController::class, 'removeProduct']);
$app->get('/recipe/commit', [RecipeController::class, 'commit']);
$app->get('/recipe/branch', [RecipeController::class, 'branch']);

//----------------------------------------------------------------
// sandbox routes
//----------------------------------------------------------------
$app->get('/sandbox', [MainSandboxController::class, 'main']);
$app->get('/sandbox/db', [DatabaseSandboxController::class, 'run']);
$app->get('/sandbox/data_manager', [DataManagerSandbox::class, 'run']);
$app->get('/sandbox/api', [ApiSandboxController::class, 'run']);
$app->get('/sandbox/validation', [ValidationSandboxController::class, 'run']);
$app->get('/sandbox/middleware/login', [MiddlewareSandboxController::class, 'loginByApiKey'])
    ->addMiddleware($container->get(OnlyAuthMiddleware::class))
    ->addMiddleware($container->get(TryLoginByApiKeyMiddleware::class))
;

//----------------------------------------------------------------
// test routes
//----------------------------------------------------------------
$app->get('/test', [MainTestController::class, 'main']);
$app->get('/test/dump', [MainTestController::class, 'testDump']);
$app->get('/test/container', [MainTestController::class, 'testContainer']);

$app->run();
