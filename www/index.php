<?php

use Zend\Diactoros\ServerRequestFactory;

define("APPLICATION_ROOT", __DIR__ . "/..");

require APPLICATION_ROOT . "/vendor/autoload.php";

$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$application = new \HttpBin\DefaultApplication();

try {
    $response = $application->dispatch($request);
    echo $application->emit($response);
} catch (Exception $e){
    echo $application->emit(new \Zend\Diactoros\Response\HtmlResponse("Internal error", 500));
}
