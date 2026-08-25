<?php

declare(strict_types=1);

namespace Shinobi;

final readonly class Deployment
{
    /** @param list<array<string, mixed>> $bindings */
    public function __construct(public array $bindings = [])
    {
    }

    /** @param array{bindings?: list<array<string, mixed>>} $definition */
    public static function fromArray(array $definition): self
    {
        return new self($definition['bindings'] ?? []);
    }
}
