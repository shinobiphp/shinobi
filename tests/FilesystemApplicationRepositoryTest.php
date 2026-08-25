<?php

declare(strict_types=1);

namespace Shinobi\Tests;

use PHPUnit\Framework\TestCase;
use Shinobi\FilesystemApplicationRepository;

final class FilesystemApplicationRepositoryTest extends TestCase
{
    public function testLoadsNamedAppManifestFromFilesystem(): void
    {
        $repository = new FilesystemApplicationRepository(dirname(__DIR__) . '/apps');

        $application = $repository->find('app://archiq#1.0');

        self::assertNotNull($application);
        self::assertSame('archiq', $application->name);
        self::assertSame('app://shinobi#1.0', $application->parent);
        self::assertSame(['config://archiq#1.0'], $application->config);
        self::assertSame(['capability://archiq#1.0'], $application->capabilities);
    }

    public function testReturnsNullForUnknownApplication(): void
    {
        $repository = new FilesystemApplicationRepository(dirname(__DIR__) . '/apps');

        self::assertNull($repository->find('app://missing#1.0'));
    }
}
