<?php

namespace StarrPHP\Core;

use ReflectionClass;
use ReflectionException;
use StarrPHP\Core\Attribute\Route;
use StarrPHP\Core\Enum\HttpStatus;
use StarrPHP\Core\Exception\HttpException;
use StarrPHP\Core\Exception\ValidationException;

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

            $this->routes[$key] = [$controllerClass, $reflectionMethod->getName(), $fullPath];
        }
    }

    public function resolve(string $uri, string $httpMethod): Response
    {
        $method = strtoupper($httpMethod);
        [$target, $params] = $this->match($uri, $method);

        if ($target === null) {
            return Response::json(['message' => 'Not Found'], HttpStatus::NotFound);
        }

        try {
            [$controllerClass, $action] = $target;
            $controller = $this->container->make($controllerClass);
            return $controller->$action(...array_values($params));
        } catch (ValidationException $e) {
            return Response::json(['message' => $e->getMessage(), 'errors' => $e->getErrors()], $e->status);
        } catch (HttpException $e) {
            return Response::json(['message' => $e->getMessage()], $e->status);
        } catch (\Throwable $e) {
            return Response::json(['message' => 'Internal Server Error'], HttpStatus::InternalServerError);
        }
    }

    private function match(string $uri, string $method): array
    {
        $exactKey = $method . ' ' . $uri;
        if (isset($this->routes[$exactKey])) {
            return [$this->routes[$exactKey], []];
        }

        foreach ($this->routes as $key => [$controllerClass, $action, $path]) {
            if (!str_starts_with($key, $method . ' ')) {
                continue;
            }

            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $path);
            $pattern = '#^' . $pattern . '$#';

            preg_match_all('/\{([^}]+)\}/', $path, $paramNames);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $params = array_combine($paramNames[1], $matches);
                return [[$controllerClass, $action], $params];
            }
        }

        return [null, []];
    }
}