<?php

declare(strict_types=1);

namespace Shinobi\Tests;

use PHPUnit\Framework\TestCase;
use Shinobi\ApplicationExecutor;
use Shinobi\ApplicationResolver;
use Shinobi\BindingResolver;
use Shinobi\Deployment;
use Shinobi\FilesystemApplicationRepository;
use Shinobi\HttpRequest;
use Shinobi\Node;

final class NodeTest extends TestCase
{
    public function testRoutesAnHttpRequestToTheBoundApplication(): void
    {
        $root = sys_get_temp_dir() . '/shinobi-node-' . bin2hex(random_bytes(4));
        mkdir($root . '/demo', 0777, true);

        try {
            file_put_contents($root . '/demo/demo.app', "name: demo\nhandler: Shinobi\\Tests\\TestHttpHandler\n");

            $node = new Node(
                new Deployment([
                    [
                        'transport' => 'http',
                        'host' => ['demo.test', 'www.demo.test'],
                        'port' => [80, 9501],
                        'app' => 'app://demo#1.0',
                    ],
                ]),
                new BindingResolver([
                    [
                        'transport' => 'http',
                        'host' => ['demo.test', 'www.demo.test'],
                        'port' => [80, 9501],
                        'app' => 'app://demo#1.0',
                    ],
                ]),
                new ApplicationResolver(new FilesystemApplicationRepository($root)),
                new ApplicationExecutor(),
            );

            $response = $node->handle(new HttpRequest('http', 'www.demo.test', 9501, 'GET', '/'));

            self::assertSame(200, $response->status);
            self::assertSame('demo', $response->body);
        } finally {
            @unlink($root . '/demo/demo.app');
            @rmdir($root . '/demo');
            @rmdir($root);
        }
    }
}
