<?php

namespace StarrPHP\Core\Exception;

use StarrPHP\Core\Enum\HttpStatus;

class ValidationException extends HttpException
{
    public function __construct(private array $errors)
    {
        parent::__construct('Validation failed.', HttpStatus::UnprocessableEntity);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
