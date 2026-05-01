<?php

namespace StarrPHP\Core\Validation;

interface ValidationRule
{
    public function validate(string $field, mixed $value): ?string;
}
