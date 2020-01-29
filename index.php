<?php

use Controllers\ApiController;
use Controllers\HomeController;
use Routing\Router;

spl_autoload_register(function ($className) {
    if (file_exists(($file = str_replace("\\", "/", $className) . '.php'))) {
        include $file;
    }
});

$router = new Router();

$router->register('/', HomeController::class, "home");

// API routes
$router->register('/api/v1/stations', ApiController::class, 'stations');
$router->register('/api/v1/station/?', ApiController::class, 'station');
$router->register('/api/v1/weather/latest', ApiController::class, 'weather');


$route = $router->match();
if ($route) {
    $className = $route->controller();
    $controller = new $className;
    try {
        $controller->{$route->method()}(...$route->options());
    } catch (Exception $exception) {
        // @TODO handle any leftover error or error page
        include "Views/404.php";
    }
} else {
    include "Views/404.php";
}