<?php

use Controllers\ApiController;
use Controllers\HomeController;
use Routing\Router;

spl_autoload_register(function($className) {
    if (file_exists(($file = str_replace("\\", "/", $className) . '.php'))) {
        include $file;
    }
});

$router = new Router();

$router->register('', HomeController::class, "home");

// API routes
$router->register('/api/v1/stations', ApiController::class, 'stations');
// /api/v1/station/{id}/weather
// /api/v1/weather/current
// /api/v1/weather/


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
        include "Views/404.php";
    }
}else{
    include "Views/404.php";
}