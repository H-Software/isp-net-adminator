<?php

require __DIR__ . '/vendor/autoload.php';

// use JeremyKendall\Password\PasswordValidator;

// $v = new PasswordValidator();

// echo $v->rehash("pass");

$rosConfig = new \RouterOS\Config([
    'host' => '192.168.1.213',
    'user' => 'admin',
    'pass' => '',
    'port' => 18728,
]);
$rosClient = new \RouterOS\Client($rosConfig);

$query = (new \RouterOS\Query('/ip/address/print'));

// Send query and read response from RouterOS
$response = $rosClient->query($query)->read();

echo "<pre>" . var_export($response, true) . "</pre>";
