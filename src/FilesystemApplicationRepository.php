<?php

declare(strict_types=1);

namespace Shinobi;

use Nette\Neon\Neon;
use RuntimeException;

final class FilesystemApplicationRepository implements ApplicationRepository
{
    public function __construct(private readonly string $root)
    {
    }

    public function find(string $uri): ?Application
    {
        [$name] = array_pad(explode('#', $this->applicationName($uri), 2), 1, null);
        $path = $this->root . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $name . '.app';

        if (!is_file($path)) {
            return null;
        }

        $definition = Neon::decodeFile($path);
        if (!is_array($definition)) {
            throw new RuntimeException(sprintf('Application manifest must contain a mapping: %s.', $path));
        }

        return Application::fromArray($definition);
    }

    private function applicationName(string $uri): string
    {
        $prefix = 'app://';
        if (!str_starts_with($uri, $prefix)) {
            throw new RuntimeException(sprintf('Invalid application URI: %s.', $uri));
        }

        $name = substr($uri, strlen($prefix));
        if ($name === '' || preg_match('/^[A-Za-z0-9_-]+(?:#[^#]+)?$/', $name) !== 1) {
            throw new RuntimeException(sprintf('Invalid application URI: %s.', $uri));
        }

        return $name;
    }
}
