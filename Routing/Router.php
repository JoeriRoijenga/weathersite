<?php

namespace Routing;

class Router
{

    const GET = 'GET';
    const POST = 'POST';

    private $routes = [];

    public function register($url, $controller, $method, $httpMethod = self::GET){
        $this->routes[$url] = new Route($controller, $method, $httpMethod);
    }

    public function match(){
        $url = $_GET['req'] ?? '/';
        $httpMethod = strtoupper(filter_input( \INPUT_SERVER, 'REQUEST_METHOD', \FILTER_SANITIZE_SPECIAL_CHARS));

        foreach ($this->routes as $route => $method){
            $test = '/^' . preg_replace('/\?/', '([^\/]+)', preg_replace('/\//', '\/', $route)) . '$/';
            if (preg_match($test, $url, $options)){
                array_shift($options);
                $routeMatch = $this->routes[$route];

                if(!in_array($httpMethod, $routeMatch->httpMethods())){
                    continue;
                }

                $routeMatch->setOptions($options);
                return $routeMatch;
            }

        }

        return false;
    }

}