<?php

namespace App\Core\Routing;

use App\Core\Request\Request;
use App\Core\Routing\Route;

class Router
{
    private array $routes;
    private $currentRoute;
    private object $request;

    public function __construct()
    {
        $this->request = new Request();
        $this->routes = Route::getRoutes();
        $this->currentRoute = $this->findCurrentRoute($this->request);
    }

    private function findCurrentRoute(Request $request)
    {
        foreach ($this->routes as $route) {
            if ($this->regexMatch($route['uri']) && $this->request->method == $route['method']) {
                return $route;
            }
        }
        return null;
    }

    private function regexMatch($route)
    {
        $pattern = '/^' . str_replace(['/', '{', '}'], ['\/', '(?<', '>[-\w]+)'], $route) . '$/';
        $result = preg_match($pattern, $this->request->uri, $matches);
        if (!$result) {
            return false;
        }
        global $request;
        foreach ($matches as $key => $value) {
            if (!is_numeric($key)) {
                $request->addRouteParams($key, $value);
            }
        }
        return true;
    }

    public function run()
    {
        if ($this->currentRoute == null) {
            echo 'error 404';
            die();
        }

        $action = $this->currentRoute['action'];

        if (is_callable($action)) {
            $action();
        }

        if ($action == null) {
            return;
        }

        if (is_string($action)) {
            $controller = explode('@', $action);
            $class = 'App\Core\Controller\\' . $controller[0];
            $method = $controller[1];
            $controller = new $class();
            $controller->{$method}();
        }
    }
}