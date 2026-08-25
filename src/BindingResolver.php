<?php

declare(strict_types=1);

namespace Shinobi;

final class BindingResolver
{
    /** @param list<array<string, mixed>> $bindings */
    public function __construct(private readonly array $bindings)
    {
    }

    /** @param array<string, mixed> $endpoint */
    public function resolve(array $endpoint): ?string
    {
        $matches = [];

        foreach ($this->bindings as $index => $binding) {
            if ($this->matches($binding, $endpoint)) {
                $matches[] = [$this->specificity($binding), $index, $binding['app']];
            }
        }

        if ($matches === []) {
            return null;
        }

        usort($matches, static fn (array $a, array $b): int => $b[0] <=> $a[0] ?: $a[1] <=> $b[1]);

        return $matches[0][2];
    }

    /** @param array<string, mixed> $binding @param array<string, mixed> $endpoint */
    private function matches(array $binding, array $endpoint): bool
    {
        foreach ($binding as $key => $value) {
            if ($key === 'app') {
                continue;
            }

            if (!array_key_exists($key, $endpoint) || $endpoint[$key] !== $value) {
                return false;
            }
        }

        return isset($binding['app']) && is_string($binding['app']);
    }

    /** @param array<string, mixed> $binding */
    private function specificity(array $binding): int
    {
        return count(array_diff_key($binding, ['app' => true]));
    }
}
