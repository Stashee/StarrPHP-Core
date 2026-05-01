<?php

namespace StarrPHP\Core\Attribute\Validation;

use Attribute;
use StarrPHP\Core\Validation\ValidationRule;

#[Attribute(Attribute::TARGET_PARAMETER)]
class MinLength implements ValidationRule
{
    public function __construct(private int $min) {}

    public function validate(string $field, mixed $value): ?string
    {
        if ($value !== null && $value !== '' && mb_strlen((string) $value) < $this->min) {
            return "$field must be at least {$this->min} characters.";
        }
        return null;
    }
}
