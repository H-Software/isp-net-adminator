<?php

use Cartalyst\Sentinel\Native\SentinelBootstrapper;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

// authz
Sentinel::instance(new SentinelBootstrapper((require __DIR__ . '/settings-sentinel.php')));