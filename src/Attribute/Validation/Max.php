<?php

namespace StarrPHP\Core\Attribute\Validation;

use Attribute;
use StarrPHP\Core\Validation\ValidationRule;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Max implements ValidationRule
{
    public function __construct(private int|float $max) {}

    public function validate(string $field, mixed $value): ?string
    {
        if ($value !== null && $value !== '' && (float) $value > $this->max) {
            return "$field may not be greater than {$this->max}.";
        }
        return null;
    }
}
