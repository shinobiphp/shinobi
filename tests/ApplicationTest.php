<?php

declare(strict_types=1);

use Shinobi\Application;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    public function testApplicationDeclaresItsResourcesAndParent(): void
    {
        $application = Application::fromArray([
            'extends' => 'app://shinobi#1.0',
            'name' => 'archiq',
            'config' => ['config://archiq#1.0'],
            'capabilities' => ['capability://archiq#1.0'],
            'commands' => ['command://archiq#1.0'],
        ]);

        self::assertSame('archiq', $application->name);
        self::assertSame('app://shinobi#1.0', $application->parent);
        self::assertSame(['config://archiq#1.0'], $application->config);
        self::assertSame(['capability://archiq#1.0'], $application->capabilities);
        self::assertSame(['command://archiq#1.0'], $application->commands);
    }
}
