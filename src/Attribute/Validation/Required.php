<?php

namespace StarrPHP\Core\Attribute\Validation;

use Attribute;
use StarrPHP\Core\Validation\ValidationRule;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Required implements ValidationRule
{
    public function validate(string $field, mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return "$field is required.";
        }
        return null;
    }
}
