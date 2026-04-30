<?php

namespace StarrPHP\Core\Exception;

use StarrPHP\Core\Enum\HttpStatus;

class NotFoundException extends HttpException
{
    public function __construct(string $message = 'Not Found')
    {
        parent::__construct($message, HttpStatus::NotFound);
    }
}
