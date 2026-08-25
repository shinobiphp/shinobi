<?php

declare(strict_types=1);

namespace Shinobi\Tests;

use PHPUnit\Framework\TestCase;
use Shinobi\BindingResolver;

final class BindingResolverTest extends TestCase
{
    public function testMostSpecificBindingWins(): void
    {
        $resolver = new BindingResolver([
            ['transport' => 'http', 'host' => 'example.com', 'app' => 'app://default#1.0'],
            ['transport' => 'http', 'host' => 'example.com', 'tenant' => 'acme', 'app' => 'app://acme#1.0'],
        ]);

        self::assertSame('app://acme#1.0', $resolver->resolve([
            'transport' => 'http',
            'host' => 'example.com',
            'tenant' => 'acme',
        ]));
    }

    public function testNatsSubjectCanBindAnApplication(): void
    {
        $resolver = new BindingResolver([
            ['transport' => 'nats', 'subject' => 'archiq.commands', 'app' => 'app://archiq#1.0'],
        ]);

        self::assertSame('app://archiq#1.0', $resolver->resolve([
            'transport' => 'nats',
            'subject' => 'archiq.commands',
        ]));
    }

    public function testReturnsNullWhenNothingMatches(): void
    {
        $resolver = new BindingResolver([
            ['transport' => 'http', 'host' => 'example.com', 'app' => 'app://default#1.0'],
        ]);

        self::assertNull($resolver->resolve([
            'transport' => 'nats',
            'subject' => 'other',
        ]));
    }
}
