<?php

declare(strict_types=1);

namespace Shinobi\Tests;

use PHPUnit\Framework\TestCase;
use Shinobi\ApplicationExecutor;
use Shinobi\ApplicationResolver;
use Shinobi\FilesystemApplicationRepository;
use Shinobi\HttpRequest;
use Shinobi\HttpResponse;

final class ApplicationExecutorTest extends TestCase
{
    public function testExecutesTheResolvedApplicationHandler(): void
    {
        $root = sys_get_temp_dir() . '/shinobi-app-' . bin2hex(random_bytes(4));
        mkdir($root . '/demo', 0777, true);

        try {
            file_put_contents($root . '/demo/demo.app', "name: demo\nhandler: Shinobi\\Tests\\TestHttpHandler\n");

            $application = new ApplicationResolver(new FilesystemApplicationRepository($root))->resolve('app://demo#1.0');
            $response = new ApplicationExecutor()->execute($application, new HttpRequest(
                transport: 'http',
                host: 'demo.test',
                port: 9501,
                method: 'GET',
                path: '/',
            ));

            self::assertSame(200, $response->status);
            self::assertSame('demo', $response->body);
        } finally {
            @unlink($root . '/demo/demo.app');
            @rmdir($root . '/demo');
            @rmdir($root);
        }
    }
}

final class TestHttpHandler
{
    public function __invoke(HttpRequest $request): HttpResponse
    {
        return new HttpResponse(body: 'demo');
    }
}
