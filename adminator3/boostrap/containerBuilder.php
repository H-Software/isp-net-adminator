<?php

use DI\ContainerBuilder;
use DI\DependencyException;

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/container.php');
$container = $builder->build();
