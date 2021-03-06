<?php

//ini_set('display_errors', 0);

use Symfony\Component\ClassLoader\DebugClassLoader;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;

require_once __DIR__.'/../vendor/autoload.php';


error_reporting(-1);
DebugClassLoader::enable();
ErrorHandler::register();

if ('cli' !== php_sapi_name()) {
    ExceptionHandler::register();
}
//remove this above



$app = require __DIR__.'/../application/app.php';
require __DIR__.'/../application/controllers.php';
$app->run();