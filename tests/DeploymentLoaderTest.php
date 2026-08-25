<?php

declare(strict_types=1);

namespace Shinobi\Tests;

use Nette\Neon\Neon;
use PHPUnit\Framework\TestCase;
use Shinobi\BindingResolver;
use Shinobi\DeploymentLoader;

final class DeploymentLoaderTest extends TestCase
{
    public function testLoadsDeploymentScrollAndResolvesBinding(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'shinobi-deployment-');
        self::assertNotFalse($path);

        try {
            Neon::encode([
                'version' => '1.0',
                'bindings' => [
                    [
                        'transport' => 'http',
                        'host' => 'example.test',
                        'port' => 9501,
                        'app' => 'app://shinobi#1.0',
                    ],
                ],
            ], Neon::BLOCK | Neon::SPACES, $path);

            $deployment = new DeploymentLoader($path)->load();
            $app = new BindingResolver($deployment->bindings)->resolve([
                'transport' => 'http',
                'host' => 'example.test',
                'port' => 9501,
            ]);

            self::assertSame('app://shinobi#1.0', $app);
        } finally {
            @unlink($path);
        }
    }
}
