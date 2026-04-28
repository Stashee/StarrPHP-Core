<?php

namespace StarrPHP\Core;

class Response
{
    public function __construct(
        private string $body,
        private int $status = 200,
        private array $headers = ['Content-Type' => 'application/json'],
    ) {}

    public static function json(mixed $data, int $status = 200): self
    {
        return new self(json_encode($data), $status);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        echo $this->body;
    }
}