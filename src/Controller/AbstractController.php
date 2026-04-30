<?php

namespace StarrPHP\Core\Controller;

use StarrPHP\Core\Enum\HttpStatus;
use StarrPHP\Core\Response;

abstract class AbstractController
{
    protected function json(mixed $data, HttpStatus $status = HttpStatus::OK): Response
    {
        return Response::json($data, $status);
    }
}