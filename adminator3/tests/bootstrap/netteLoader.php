<?php

$loader = new Nette\Loaders\RobotLoader();

// $loader->addDirectory(__DIR__ . '/../app/Auth');
$loader->addDirectory(__DIR__ . '/../../app/Core');
$loader->addDirectory(__DIR__ . '/../../app/Migration');
// $loader->addDirectory(__DIR__ . '/../app/Middleware');
// $loader->addDirectory(__DIR__ . '/../app/Middleware');
// $loader->addDirectory(__DIR__ . '/../app/Models');
// $loader->addDirectory(__DIR__ . '/../app/Validation');
$loader->addDirectory(__DIR__ . '/../../app/View');
$loader->addDirectory(__DIR__ . '/../../app/Renderer');

$loader->setTempDirectory(__DIR__ . '/../../temp');
$loader->register();
