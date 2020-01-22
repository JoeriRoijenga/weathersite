<?php

use Controllers\ApiController;
use Routing\Router;

spl_autoload_register(function($className) {
    if (file_exists(($file = $className . '.php'))) {
        include $file;
    }
});

$router = new Router();

// Option 1 register url with controller class and method
$router->register('', ApiController::class, "index");

// Option 2 Route with controller method params
//$router->register('home/?/?', HomeController::class, "home");

// Option 3 Specific HTTP methods
//$router->register('anyurl', HomeController::class, "home", Router::POST);
//$router->register('anyurl', HomeController::class, "home", [Router::GET, Router::POST]);

$route = $router->match();
if($route) {
    $className = $route->controller();
    $controller = new $className;
    try{
        $controller->{$route->method()}(...$route->options());
    }catch (Exception $exception){
        // @TODO handle any leftover error or error page
        include "Views\\404.php";
    }
}else{
    include "Views\\404.php";
}