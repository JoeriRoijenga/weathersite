<?php

use Controllers\ApiController;
use Controllers\HomeController;
use Controllers\MapController;
use Controllers\LoginController;
use Controllers\Admin\HomeController as AdminHomeController;
use Controllers\Admin\UserEditController;
use Controllers\Admin\UserAddController;
use Controllers\Admin\PrecipitationController;
use Routing\Router;

spl_autoload_register(function ($className) {
    if (file_exists(($file = str_replace("\\", "/", $className) . '.php'))) {
        include $file;
    }
});

$router = new Router();

// Normal routes
$router->register('/', HomeController::class, "home");
$router->register('/map', MapController::class, "map");
$router->register('/login', LoginController::class, "login", Router::POST);

// Admin routes
$router->register('/admin/home', AdminHomeController::class, "home");
$router->register('/admin/edituser', UserEditController::class, "edituser");
$router->register('/admin/adduser', UserAddController::class, "adduser", Router::POST);
$router->register('/precipitation', PrecipitationController::class, "precipitation");

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