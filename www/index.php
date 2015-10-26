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

$defaultRoutes = get_cfg_var("httpbin.skipDefaultRoutes");
if($defaultRoutes === true){
    $application = new \HttpBin\Application();
}else{
    $application = new \HttpBin\DefaultApplication();
}


$additionalRoutes = get_cfg_var("httpbin.handler");
if($additionalRoutes){
    if(file_exists($additionalRoutes)){
        $parsed = json_decode(file_get_contents($additionalRoutes), true);
        if(is_array($parsed)){
            foreach($parsed as $route){
                $application->getRouter()->fromArray($route);
            }
        }
    }else{
        throw new \Exception("ini rule httpbin.handler refers to an unexisting file: $additionalRoutes");
    }
}




try {
    $response = $application->dispatch($request);

} catch (Exception $e){
    $response = new \Zend\Diactoros\Response\HtmlResponse("Internal error", 500);
}

$application->emit($response);
