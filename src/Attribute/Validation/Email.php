<?php

namespace StarrPHP\Core\Attribute\Validation;

use Attribute;
use StarrPHP\Core\Validation\ValidationRule;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Email implements ValidationRule
{
    public function validate(string $field, mixed $value): ?string
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return "$field must be a valid email address.";
        }
        return null;
    }
}
