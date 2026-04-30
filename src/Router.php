<?php

namespace StarrPHP\Core;

use ReflectionClass;
use ReflectionException;
use StarrPHP\Core\Attribute\Route;
use StarrPHP\Core\Enum\HttpStatus;

class Router
{
    protected array $routes = [];

    public function __construct(private Container $container) {}

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function setRoutes(array $routes): void
    {
        $this->routes = $routes;
    }

    /**
     * @throws ReflectionException
     */
    public function registerController(string $controllerClass): void
    {
        $reflection = new ReflectionClass($controllerClass);
        $classAttributes = $reflection->getAttributes(Route::class);

        if (empty($classAttributes)) {
            return;
        }

        $prefix = rtrim($classAttributes[0]->newInstance()->path ?? '', '/');

        foreach ($reflection->getMethods() as $reflectionMethod) {
            $methodAttributes = $reflectionMethod->getAttributes(Route::class);

            if (empty($methodAttributes)) {
                continue;
            }

            /** @var Route $route */
            $route = $methodAttributes[0]->newInstance();
            $subPath = $route->path !== null ? '/' . ltrim($route->path, '/') : '';
            $fullPath = $prefix . $subPath ?: '/';

            $key = strtoupper($route->method) . ' ' . $fullPath;

            $this->routes[$key] = [$controllerClass, $reflectionMethod->getName()];
        }
    }

    public function resolve(string $uri, string $httpMethod): Response
    {
        $key = strtoupper($httpMethod) . ' ' . $uri;
        $target = $this->routes[$key] ?? null;

        if ($target === null) {
            return Response::json(['message' => 'Not Found'], HttpStatus::NotFound);
        }

        [$controllerClass, $method] = $target;
        $controller = $this->container->make($controllerClass);
        return $controller->$method();
    }
}