<?php

namespace StarrPHP\Core;

use StarrPHP\Core\Enum\HttpStatus;

readonly class Response
{
    public function __construct(
        private string     $body,
        private HttpStatus $status = HttpStatus::OK,
        private array      $headers = ['Content-Type' => 'application/json'],
    ) {}

    public static function json(mixed $data, HttpStatus $status = HttpStatus::OK): self
    {
        return new self(json_encode($data), $status);
    }

    public function send(): void
    {
        http_response_code($this->status->value);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        echo $this->body;
    }
}