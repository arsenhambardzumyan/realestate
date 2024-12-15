<?php

namespace app\Utils;

use ReflectionClass;
use app\Utils\ResponseHelper;

class Router
{
    private $routes = [];

    public function add($method, $uri, $action)
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'uri' => $uri,
            'action' => $action,
        ];
    }

    public function dispatch($requestUri, $requestMethod)
    {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $requestUri && $route['method'] === strtoupper($requestMethod)) {
                if (is_callable($route['action'])) {
                    return call_user_func($route['action']);
                }

                if (is_string($route['action'])) {
                    return $this->callControllerAction($route['action']);
                }
            }
        }

        ResponseHelper::error('Route not found', 404);
    }

    private function callControllerAction($action)
    {
        [$controller, $method] = explode('@', $action);

        // Build fully qualified namespace for the controller
        $controllerClass = "app\\Controllers\\{$controller}";

        // Include controller file dynamically
        $controllerFile = __DIR__ . "/../Controllers/{$controller}.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            ResponseHelper::error("Controller file {$controllerFile} not found", 500);
        }

        // Ensure the controller class exists
        if (!class_exists($controllerClass)) {
            ResponseHelper::error("Controller {$controllerClass} not found", 500);
        }

        // Resolve dependencies dynamically
        $instance = $this->resolveControllerDependencies($controllerClass);

        // Check if the method exists in the controller
        if (!method_exists($instance, $method)) {
            ResponseHelper::error("Method {$method} not found in {$controllerClass}", 500);
        }

        return $instance->$method();
    }

    private function resolveControllerDependencies($controller)
    {
        $reflectionClass = new ReflectionClass($controller);
        $constructor = $reflectionClass->getConstructor();

        // If no constructor, instantiate without dependencies
        if (is_null($constructor)) {
            return new $controller();
        }

        // Resolve dependencies dynamically
        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $dependencyClass = $parameter->getType()?->getName();

            if ($dependencyClass) {
                $dependencies[] = $this->resolveDependency($dependencyClass);
            } else {
                ResponseHelper::error("Cannot resolve parameter {$parameter->getName()} in {$controller}", 500);
            }
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }

    private function resolveDependency($dependency)
    {
        // Handle standard PHP objects like PDO
        if ($dependency === 'PDO') {
            $config = require __DIR__ . '/../../config/db.php';
            return new \PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']}",
                $config['user'],
                $config['password']
            );
        }

        // Dynamically include service or repository files
        $dependencyFile = __DIR__ . '/..' . str_replace('app', '', str_replace('\\', '/', $dependency)) . '.php';
        if (file_exists($dependencyFile)) {
            require_once $dependencyFile;
        } else {
            ResponseHelper::error("Dependency file {$dependencyFile} not found for {$dependency}", 500);
        }

        // Ensure the class exists
        if (!class_exists($dependency)) {
            ResponseHelper::error("Dependency class {$dependency} not found", 500);
        }

        $reflectionClass = new ReflectionClass($dependency);
        $constructor = $reflectionClass->getConstructor();

        // If no constructor, instantiate without dependencies
        if (is_null($constructor)) {
            return new $dependency();
        }

        // Resolve nested dependencies for the service constructor
        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $nestedDependencyClass = $parameter->getType()?->getName();
            if ($nestedDependencyClass) {
                $dependencies[] = $this->resolveDependency($nestedDependencyClass);
            } else {
                ResponseHelper::error("Cannot resolve parameter {$parameter->getName()} in {$dependency}", 500);
            }
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}
