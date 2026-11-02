<?php


declare(strict_types=1);

use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';


$containerBuilder = new ContainerBuilder();

$settings = require __DIR__ . '/settings.php';
$settings($containerBuilder);

$dependencies = require __DIR__ . '/dependencies.php';
$dependencies($containerBuilder);

$doctrine = require __DIR__ . '/doctrine.php';
$doctrine($containerBuilder);

$messages = require __DIR__ . '/messagebus.php';
$messages($containerBuilder);

$repositories = require __DIR__ . '/repositories.php';
$repositories($containerBuilder);

return $containerBuilder->build();
