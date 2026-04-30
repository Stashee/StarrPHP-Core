<?php

namespace StarrPHP\Core\Exception;

use StarrPHP\Core\Enum\HttpStatus;

class HttpException extends \RuntimeException
{
    public function __construct(
        string $message,
        public readonly HttpStatus $status = HttpStatus::InternalServerError
    ) {
        parent::__construct($message);
    }
}
