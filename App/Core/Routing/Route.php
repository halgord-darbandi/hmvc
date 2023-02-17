<?php

namespace App\Core\Routing;

class Route
{
    private static array $routes;

    public static function addRoute($method, string $uri, $action = null)
    {
        self::$routes[] = ['method' => strtoupper($method), 'uri' =>'/hmvc'.$uri, 'action' => $action];
    }

    public static function __callStatic($method, $params)
    {
        self::addRoute(strtoupper($method), ...$params);
    }

    public static function getRoutes()
    {
        return self::$routes;
    }
}