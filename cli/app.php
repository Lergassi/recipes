#!/usr/bin/env php
<?php
//todo: Только для dev.
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use App\Command\Admin\AddUserGroupCommand;
use App\Command\Admin\RemoveUserGroupCommand;
use App\Command\Admin\ShowUsersCommand;
use App\Debug\InitCustomDumper;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Symfony\Component\Console\Application;

//----------------------------------------------------------------
// init app
//----------------------------------------------------------------
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

new InitCustomDumper();

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAttributes(true); //todo: Сделать один контейнер на всю программу.
$containerBuilder->addDefinitions(__DIR__ . '/../app/container.php');

$container = $containerBuilder->build();

$app = new Application(
    $_ENV['APP_NAME'] ?? '',
    $_ENV['APP_VERSION'] ?? '',
);

//----------------------------------------------------------------
// commands
//----------------------------------------------------------------

//----------------------------------------------------------------
// admin commands
//----------------------------------------------------------------
$app->add($container->get(ShowUsersCommand::class));
$app->add($container->get(AddUserGroupCommand::class));
$app->add($container->get(RemoveUserGroupCommand::class));

//----------------------------------------------------------------
// todo: Только для dev.
// sandbox commands
//----------------------------------------------------------------

//----------------------------------------------------------------
// todo: Только для dev.
// test commands
//----------------------------------------------------------------


//----------------------------------------------------------------
// run app
//----------------------------------------------------------------
$app->run();