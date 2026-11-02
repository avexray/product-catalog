<?php

declare(strict_types=1);

error_reporting(E_ALL & ~E_DEPRECATED);

use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;


require __DIR__ . '/vendor/autoload.php';


// Migrations config file, see https://www.doctrine-project.org/projects/doctrine-migrations/en/3.6/reference/configuration.html
$config = new PhpFile(__DIR__ . '/app/migrations.php');

// Adapt to your DB connection parameters or re-use config file from your application
$dbParams = [
    'driver' => 'pdo_mysql',
    'host' => getenv('DATABASE_HOST'),
    'port' => getenv('DATABASE_PORT'),
    'dbname' => getenv('DATABASE_NAME'),
    'user' => getenv('DATABASE_USER'),
    'password' => getenv('DATABASE_PASSWORD'),
    'charset' => 'utf8mb4'
];
$connection = DriverManager::getConnection( $dbParams );

// This assumes annonations/attributes for ORM mapping
$isDev = true;
$doctrineConfig = ORMSetup::createAttributeMetadataConfiguration(
    [__DIR__ . '/src/Domain'],
    $isDev
);

$doctrineConfig->enableNativeLazyObjects(true);



$entityManager = new EntityManager( $connection, $doctrineConfig );
return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($entityManager));