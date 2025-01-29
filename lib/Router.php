<?php

namespace lib;
class Router
{
    private $routes = [];
    private $middleware = [];
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function add($method, $path, $handler, $middleware = [])
    {
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function get($path, $handler, $middleware = [])
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post($path, $handler, $middleware = [])
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    private function executeMiddleware($middleware, $next)
    {
        if (empty($middleware)) {
            return $next();
        }

        $middlewareClass = array_shift($middleware);
        $instance = new $middlewareClass();

        return $instance->handle(function () use ($middleware, $next) {
            return $this->executeMiddleware($middleware, $next);
        });
    }

    public function dispatch($method, $uri)
    {
        foreach ($this->routes[$method] ?? [] as $route => $config) {
            $pattern = preg_replace('/:\w+/', '(\w+)', $route);
            $pattern = "@^" . $pattern . "$@D";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove the full match

                $handler = $config['handler'];
                $middleware = $config['middleware'];

                return $this->executeMiddleware($middleware, function () use ($handler, $matches) {
                    [$controller, $action] = $handler;
                    $controller = new $controller();
                    return $controller->$action(...$matches);
                });
            }
        }

        throw new Exception('Route not found', 404);
    }
}