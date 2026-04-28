<?php

namespace StarrPHP\Core\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public ?string $path = null,
        public string $method = 'GET',
    ) {}
}
