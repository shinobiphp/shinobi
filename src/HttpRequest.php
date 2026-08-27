<?php

declare(strict_types=1);

namespace Shinobi;

final readonly class HttpRequest
{
    /** @param array<string, string> $headers */
    public function __construct(
        public string $transport,
        public string $host,
        public int $port,
        public string $method,
        public string $path,
        public array $headers = [],
        public string $body = '',
    ) {
    }
}
