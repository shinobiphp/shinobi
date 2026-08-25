<?php

declare(strict_types=1);

namespace Shinobi;

use RuntimeException;

final class ApplicationResolver
{
    /** @param array<string, Application> $applications */
    public function __construct(private readonly array $applications)
    {
    }

    public function resolve(string $uri): Application
    {
        return $this->resolveApplication($uri, []);
    }

    /** @param list<string> $stack */
    private function resolveApplication(string $uri, array $stack): Application
    {
        if (in_array($uri, $stack, true)) {
            throw new RuntimeException(sprintf('Circular application inheritance detected: %s.', implode(' -> ', [...$stack, $uri])));
        }

        $application = $this->applications[$uri] ?? null;
        if ($application === null) {
            throw new RuntimeException(sprintf('Application not found: %s.', $uri));
        }

        if ($application->parent === null) {
            return $application;
        }

        $parent = $this->resolveApplication($application->parent, [...$stack, $uri]);

        return Application::compose($parent, $application);
    }
}
