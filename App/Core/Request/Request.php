<?php

namespace App\Core\Request;

class Request
{
    private string $method;
    private string $uri;
    private $routeParams;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function addRouteParams($key, $value)
    {
        $this->routeParams[$key] = $value;
    }

    public function getRouteParam($key)
    {
        return $this->routeParams[$key];
    }

    public function getRouteParams()
    {
        return $this->routeParams;
    }
}