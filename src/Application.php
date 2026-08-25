<?php

declare(strict_types=1);

namespace Shinobi;

final readonly class Application
{
    private function __construct(
        public string $name,
        public ?string $parent,
        public array $config,
        public array $capabilities,
        public array $commands,
    ) {
    }

    /** @param array{name: string, extends?: string, config?: list<string>, capabilities?: list<string>, commands?: list<string>} $definition */
    public static function fromArray(array $definition): self
    {
        return new self(
            name: $definition['name'],
            parent: $definition['extends'] ?? null,
            config: $definition['config'] ?? [],
            capabilities: $definition['capabilities'] ?? [],
            commands: $definition['commands'] ?? [],
        );
    }
}
