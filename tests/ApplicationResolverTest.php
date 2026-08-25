<?php

declare(strict_types=1);

namespace Shinobi\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Shinobi\Application;
use Shinobi\ApplicationRepository;
use Shinobi\ApplicationResolver;

final class ApplicationResolverTest extends TestCase
{
    public function testResolvesParentAndChildApplications(): void
    {
        $resolver = new ApplicationResolver($this->repository([
            'app://shinobi#1.0' => Application::fromArray([
                'name' => 'shinobi',
                'config' => ['config://shinobi#1.0'],
                'capabilities' => ['capability://runtime#1.0'],
                'commands' => ['cmd://shinobi#1.0'],
            ]),
            'app://archiq#1.0' => Application::fromArray([
                'name' => 'archiq',
                'extends' => 'app://shinobi#1.0',
                'config' => ['config://archiq#1.0'],
                'capabilities' => ['capability://archiq#1.0'],
            ]),
        ]));

        $resolved = $resolver->resolve('app://archiq#1.0');

        self::assertSame('archiq', $resolved->name);
        self::assertSame(['config://shinobi#1.0', 'config://archiq#1.0'], $resolved->config);
        self::assertSame(['capability://runtime#1.0', 'capability://archiq#1.0'], $resolved->capabilities);
        self::assertSame(['cmd://shinobi#1.0'], $resolved->commands);
    }

    public function testRejectsMissingParent(): void
    {
        $resolver = new ApplicationResolver($this->repository([
            'app://archiq#1.0' => Application::fromArray([
                'name' => 'archiq',
                'extends' => 'app://missing#1.0',
            ]),
        ]));

        $this->expectException(RuntimeException::class);
        $resolver->resolve('app://archiq#1.0');
    }

    public function testRejectsCircularInheritance(): void
    {
        $resolver = new ApplicationResolver($this->repository([
            'app://a#1.0' => Application::fromArray([
                'name' => 'a',
                'extends' => 'app://b#1.0',
            ]),
            'app://b#1.0' => Application::fromArray([
                'name' => 'b',
                'extends' => 'app://a#1.0',
            ]),
        ]));

        $this->expectException(RuntimeException::class);
        $resolver->resolve('app://a#1.0');
    }

    /** @param array<string, Application> $applications */
    private function repository(array $applications): ApplicationRepository
    {
        return new class ($applications) implements ApplicationRepository {
            public function __construct(private readonly array $applications)
            {
            }

            public function find(string $uri): ?Application
            {
                return $this->applications[$uri] ?? null;
            }
        };
    }
}
