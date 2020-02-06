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

// Start session
session_start();

// Create route
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


// Check if route exists
$route = $router->match();
if ($route) {
    // Set class name
    $className = $route->controller();
    $controller = new $className;

    // Call page
    try {
        $controller->{$route->method()}(...$route->options());
    } catch (Exception $exception) {
        (new PageController())->notFound(500);
    }
} else {
    // Page not found, 404 error
    (new PageController())->notFound(404);
}