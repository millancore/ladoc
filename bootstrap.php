<?php

$autoloaders = [
    // it's installed Globally
    __DIR__ . '/../../autoload.php',

    // it's installed Locally
    __DIR__ . '/vendor/autoload.php'
];

foreach ($autoloaders as $file) {
    if (file_exists($file)) {
        $autoloader = $file;
        break;
    }
}

if (!isset($autoloader)) {
    echo 'You must set up the project dependencies using `composer install`' . PHP_EOL;
    exit(1);
}


require_once $autoloader;
