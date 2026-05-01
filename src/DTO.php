<?php

namespace StarrPHP\Core;

use ReflectionClass;

abstract readonly class DTO
{
    public static function fromRequest(Request $request): static
    {
        $data = $request->body();

        (new Validator())->validate(static::class, $data);

        $reflection = new ReflectionClass(static::class);
        $args = [];

        foreach ($reflection->getConstructor()->getParameters() as $param) {
            $args[] = $data[$param->getName()] ?? null;
        }

        return $reflection->newInstanceArgs($args);
    }
}
