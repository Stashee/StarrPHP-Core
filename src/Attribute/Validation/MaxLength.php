<?php

namespace StarrPHP\Core\Attribute\Validation;

use Attribute;
use StarrPHP\Core\Validation\ValidationRule;

#[Attribute(Attribute::TARGET_PARAMETER)]
class MaxLength implements ValidationRule
{
    public function __construct(private int $max) {}

    public function validate(string $field, mixed $value): ?string
    {
        if ($value !== null && mb_strlen((string) $value) > $this->max) {
            return "$field may not be longer than {$this->max} characters.";
        }
        return null;
    }
}
