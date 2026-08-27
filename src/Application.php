<?php

declare(strict_types=1);

namespace Shinobi;

final readonly class Application
{
    private function __construct(
        public string $name,
        public ?string $parent,
        public ?string $handler,
        public array $config,
        public array $capabilities,
        public array $commands,
    ) {
    }

    /** @param array{name: string, extends?: string, handler?: string, config?: list<string>, capabilities?: list<string>, commands?: list<string>} $definition */
    public static function fromArray(array $definition): self
    {
        return new self(
            name: $definition['name'],
            parent: $definition['extends'] ?? null,
            handler: $definition['handler'] ?? null,
            config: $definition['config'] ?? [],
            capabilities: $definition['capabilities'] ?? [],
            commands: $definition['commands'] ?? [],
        );
    }

    public static function compose(self $parent, self $child): self
    {
        return new self(
            name: $child->name,
            parent: $child->parent,
            handler: $child->handler ?? $parent->handler,
            config: self::merge($parent->config, $child->config),
            capabilities: self::merge($parent->capabilities, $child->capabilities),
            commands: self::merge($parent->commands, $child->commands),
        );
    }

    private static function merge(array $parent, array $child): array
    {
        return array_values(array_unique([...$parent, ...$child]));
    }
}
