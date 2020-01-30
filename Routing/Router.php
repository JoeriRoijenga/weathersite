<?php

namespace Routing;

use Controllers\Controller;
use const FILTER_SANITIZE_SPECIAL_CHARS;
use const INPUT_SERVER;

/**
 * Class Router
 * @package Routing
 */
class Router
{

    const GET = 'GET';
    const POST = 'POST';

    private $routes = [];

    /**
     * Register URL match
     *
     * @param $url string URL to register.
     * @param $controller Controller Controller to call.
     * @param $method string Method name to call in the Controller.
     * @param string $httpMethod string HTTP method to match.
     */
    public function register($url, $controller, $method, $httpMethod = self::GET)
    {
        $this->routes[$url] = new Route($controller, $method, $httpMethod);
    }

    /**
     * Find matching route for the current Request URL.
     *
     * @return bool|Route Return Match if found.
     */
    public function match()
    {
        $url = $_GET['req'] ?? '/';
        if (substr($url, 0, 1) !== '/') {
            $url = '/' . $url;
        }

        $httpMethod = strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_SPECIAL_CHARS));

        foreach ($this->routes as $route => $method) {
            $test = '/^' . preg_replace('/\?/', '([^\/]+)', preg_replace('/\//', '\/', $route)) . '$/';
            if (preg_match($test, $url, $options)) {
                array_shift($options);
                $routeMatch = $this->routes[$route];

                if (!in_array($httpMethod, $routeMatch->httpMethods())) {
                    continue;
                }

                $routeMatch->setOptions($options);
                return $routeMatch;
            }

        }

        return false;
    }

}