<?php

declare(strict_types=1);

require __DIR__ ."/../app/Migration/Migration.php";

require __DIR__ ."/../boostrap/database.php";

$settings = require __DIR__ . '/../config/settings.php';

$db = $settings['db'];

$settings['phinx']['environments']['default'] = [
    'adapter' => $db['driver'],
    'host'    => $db['host'] ?? null,
    'port'    => $db['port'] ?? null,
    'socket'  => $db['socket'] ?? null,
    'name'    => $db['database'],
    'user'    => $db['username'],
    'pass'    => $db['password'],
];

return $settings['phinx'];
