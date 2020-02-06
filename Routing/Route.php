<?php

namespace Routing;

use Controllers\Controller;

/**
 *
 *
 * Class Route
 * @package Routing
 */
class Route
{
    // Global variables
    private $controller;
    private $method;
    private $httpMethods = [];
    private $options = [];

    /**
     * Route constructor.
     *
     * @param $controller Controller Controller to call.
     * @param $method string Method name to call in the Controller.
     * @param string $httpMethod string HTTP method to match.
     */
    public function __construct($controller, $method, $httpMethod)
    {
        $this->controller = $controller;
        $this->method = $method;
        if (is_array($httpMethod)) {
            $this->httpMethods = $httpMethod;
        } else {
            $this->httpMethods = [$httpMethod];
        }
    }

    /**
     * Set the URI params used in this route.
     *
     * @param $options array Array of the used params.
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Set the controller
     *
     * @return Controller
     */
    public function controller()
    {
        return $this->controller;
    }

    /**
     * Get the right method
     *
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * set HTTP method
     * @return array
     */
    public function httpMethods()
    {
        return $this->httpMethods;
    }

    /**
     * Set options
     */
    public function options()
    {
        return $this->options;
    }
}