<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$ormConfig = ORMSetup::createAnnotationMetadataConfiguration(array(__DIR__."/src/dao"), $isDevMode, $proxyDir, $cache);

$ormConn = array(
    'doctrine_driver' => 'pdo_mysql',
    'driver' => 'pdo_mysql',
    'host' => getenv("MYSQL_SERVER") ? getenv("MYSQL_SERVER") : "localhost",
    'user' => getenv("MYSQL_USER") ? getenv("MYSQL_USER") : "root",
    'password' => getenv("MYSQL_PASSWD") ? getenv("MYSQL_PASSWD") : "password",
    'dbname' => 'adminator2'
);

// obtaining the entity manager
$em = EntityManager::create($ormConn, $ormConfig);
