<?php

namespace StarrPHP\Core\Attribute\Validation;

use Attribute;
use StarrPHP\Core\Validation\ValidationRule;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Min implements ValidationRule
{
    public function __construct(private int|float $min) {}

    public function validate(string $field, mixed $value): ?string
    {
        if ($value !== null && $value !== '' && (float) $value < $this->min) {
            return "$field must be at least {$this->min}.";
        }
        return null;
    }
}
