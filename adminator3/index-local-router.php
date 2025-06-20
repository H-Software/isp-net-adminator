<?php

// stolen from https://stackoverflow.com/a/38926070 and improved

chdir(__DIR__);

$scriptName = ltrim($_SERVER["REQUEST_URI"], '/');
// fix GET in URI
list($scriptName) = explode('?', $scriptName);
// echo "filePath: ". $scriptName . "<br>";

$filePath = realpath($scriptName);

if ($filePath && is_dir($filePath)) {
    // attempt to find an index file
    foreach (['index.php'] as $indexFile) {
        if ($filePath = realpath($filePath . DIRECTORY_SEPARATOR . $indexFile)) {
            break;
        }
    }
}

if ($filePath && is_file($filePath)) {
    // 1. check that file is not outside of this directory for security
    // 2. check for circular reference to router.php
    // 3. don't serve dotfiles
    if (strpos($filePath, __DIR__ . DIRECTORY_SEPARATOR) === 0 &&
        $filePath != __DIR__ . DIRECTORY_SEPARATOR . 'index-local-router.php' &&
        substr(basename($filePath), 0, 1) != '.'
    ) {
        if (strtolower(substr($filePath, -4)) == '.php') {
            // php file; serve through interpreter
            include $filePath;
        } else {
            // asset file; serve from filesystem
            return false;
        }
    } else {
        // disallowed file
        header("HTTP/1.1 404 Not Found");
        echo "404 Not Found";
    }
} else {
    if ((preg_match("/^.+(\.php|\.js)$/", $scriptName) == 1)
            or ($scriptName == "favicon.ico")
    ) {
        header("HTTP/1.1 404 Not Found");
        echo "404 Not Found";
    } else {
        // echo "filePath: ". $scriptName . "<br>";
        // rewrite to our index file
        include __DIR__ . DIRECTORY_SEPARATOR . 'index.php';
    }
}
