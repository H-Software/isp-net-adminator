<?php

require __DIR__ . '/vendor/autoload.php';

use JeremyKendall\Password\PasswordValidator;

$v = new PasswordValidator();

echo $v->rehash("pass");
