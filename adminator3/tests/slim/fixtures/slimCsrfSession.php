<?php

// https://manuel-strehl.de/overwrite_PHP_built-in_functions

namespace Slim\Csrf;

function session_status()
{
    return PHP_SESSION_ACTIVE;
}
