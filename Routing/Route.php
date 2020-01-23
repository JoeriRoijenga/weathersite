<?php

namespace Routing;

class Route
{

    private $controller;
    private $method;
    private $httpMethods = [];
    private $options = [];

    public function __construct($controller, $method, $httpMethod)
    {
        $this->controller = $controller;
        $this->method = $method;
        if (is_array($httpMethod)){
            $this->httpMethods = $httpMethod;
        }else{
            $this->httpMethods = [$httpMethod];
        }
    }

    public function setOptions($options){
        $this->options = $options;
    }

    public function controller(){
        return $this->controller;
    }

    public function method(){
        return $this->method;
    }

    public function httpMethods(){
        return $this->httpMethods;
    }

    public function options(){
        return $this->options;
    }

}