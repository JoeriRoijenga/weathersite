<?php

use Controllers\ApiController;
use Controllers\PageController;
use Controllers\AccountController;
use Controllers\AdminController;
use Routing\Router;

spl_autoload_register(function ($className) {
    if (file_exists(($file = str_replace("\\", "/", $className) . '.php'))) {
        include $file;
    }
});

session_start();

$router = new Router();

// Static pages
$router->register('/', PageController::class, "home");
$router->register('/map', PageController::class, "map");

// Login/logout
$router->register('/login', AccountController::class, "login", [Router::GET, Router::POST]);
$router->register('/logout', AccountController::class, "logout", [Router::GET, Router::POST]);

// Admin routes
$router->register('/admin/home', AdminController::class, "home");
$router->register('/admin/users', AdminController::class, "usersOverview");
$router->register('/admin/user/add', AdminController::class, "userAdd", [Router::GET, Router::POST]);
$router->register('/admin/user/?/?', AdminController::class, "userEdit", [Router::GET, Router::POST]);

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
        include "views/404.php";
    }
} else {
    include "views/404.php";
}