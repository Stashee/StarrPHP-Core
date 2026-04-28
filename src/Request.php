<?php

namespace StarrPHP\Core;

class Request
{
    public string $method;
    public string $uri;
    private array $query;
    private array $body;
    private array $headers;

    public function __construct(
        string $method,
        string $uri,
        array $query = [],
        array $body = [],
        array $headers = []
    ) {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->query = $query;
        $this->body = $body;
        $this->headers = $headers;
    }

    public static function createFromGlobals(): self
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[str_replace('_', '-', substr($key, 5))] = $value;
            }
        }

        return new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH),
            $_GET,
            $_POST,
            $headers
        );
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function body(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    public function header(string $key): ?string
    {
        return $this->headers[strtoupper(str_replace('-', '_', $key))] ?? null;
    }

    public function isMethod(string $method): bool
    {
        return $this->method === strtoupper($method);
    }
}