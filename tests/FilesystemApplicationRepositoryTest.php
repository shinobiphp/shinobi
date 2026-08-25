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

        $application = $repository->find('app://shinobi#1.0');

        self::assertNotNull($application);
        self::assertSame('shinobi', $application->name);
        self::assertNull($application->parent);
        self::assertSame(['config://shinobi#1.0'], $application->config);
        self::assertSame(['capability://runtime#1.0'], $application->capabilities);
        self::assertSame(['cmd://shinobi#1.0'], $application->commands);
    }

    public function testReturnsNullForUnknownApplication(): void
    {
        $repository = new FilesystemApplicationRepository(dirname(__DIR__) . '/apps');

        self::assertNull($repository->find('app://missing#1.0'));
    }
}
