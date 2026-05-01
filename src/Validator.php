<?php

namespace StarrPHP\Core;

use ReflectionClass;
use StarrPHP\Core\Exception\ValidationException;
use StarrPHP\Core\Validation\ValidationRule;

class Validator
{
    public function validate(string $dtoClass, array $data): void
    {
        $reflection = new ReflectionClass($dtoClass);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return;
        }

        $errors = [];

        foreach ($constructor->getParameters() as $param) {
            $field = $param->getName();
            $value = $data[$field] ?? null;

            foreach ($param->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();

                if (!($instance instanceof ValidationRule)) {
                    continue;
                }

                $error = $instance->validate($field, $value);

                if ($error !== null) {
                    $errors[$field][] = $error;
                }
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
