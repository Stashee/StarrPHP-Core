<?php

namespace StarrPHP\Core;

use ReflectionClass;
use ReflectionException;
use RuntimeException;

class Container
{
    private array $bindings = [];

    public function bind(string $abstract, string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * @throws ReflectionException
     */
    public function make(string $class): object
    {
        $class = $this->bindings[$class] ?? $class;

        $reflection = new ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new RuntimeException("[$class] is not instantiable.");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $deps = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();

            if ($type === null || $type->isBuiltin()) {
                if ($param->isDefaultValueAvailable()) {
                    $deps[] = $param->getDefaultValue();
                    continue;
                }
                throw new RuntimeException("Cannot resolve parameter [{$param->getName()}] in [$class].");
            }

            $deps[] = $this->make($type->getName());
        }

        return $reflection->newInstanceArgs($deps);
    }
}
