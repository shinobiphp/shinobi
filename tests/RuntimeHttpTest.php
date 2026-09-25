<?php

declare(strict_types=1);

namespace Shinobi\Tests;

use Nette\Neon\Neon;
use PHPUnit\Framework\TestCase;

final class RuntimeHttpTest extends TestCase
{
    private string $root;
    private int $port;
    private mixed $process = null;
    private string $log;

    protected function setUp(): void
    {
        if (!extension_loaded('openswoole')) {
            self::markTestSkipped('OpenSwoole is required for HTTP integration tests.');
        }
        $this->root = sys_get_temp_dir() . '/shinobi-http-' . bin2hex(random_bytes(6));
        mkdir($this->root);
        foreach (['apps', 'scrolls'] as $directory) {
            $this->copyTree(dirname(__DIR__) . '/' . $directory, $this->root . '/' . $directory);
        }
        $socket = stream_socket_server('tcp://127.0.0.1:0', $errno, $error);
        self::assertNotFalse($socket, $error);
        $this->port = (int) substr(strrchr(stream_socket_get_name($socket, false), ':'), 1);
        fclose($socket);
        $this->log = $this->root . '/server.log';
    }

    protected function tearDown(): void
    {
        if (is_resource($this->process)) {
            proc_terminate($this->process);
            proc_close($this->process);
        }
        if (isset($this->root)) {
            $this->removeTree($this->root);
        }
    }

    public function testCliServesRepeatedRequestsWithEnvironmentOverrides(): void
    {
        $this->start([PHP_BINARY, dirname(__DIR__) . '/bin/shinobi', 'run'], [
            'SHINOBI_HOST' => '127.0.0.1',
            'SHINOBI_PORT' => (string) $this->port,
        ]);
        foreach (['/first', '/second'] as $path) {
            [$status, $body] = $this->request($path);
            self::assertSame(200, $status);
            self::assertSame(['name' => 'shinobi', 'status' => 'ok', 'method' => 'GET', 'path' => $path], json_decode($body, true));
        }
        self::assertSame(404, $this->request('/', 'unbound.test')[0]);
    }

    public function testMountedChildAppInheritsSpecAndRuntimeAndExecutesItsHandler(): void
    {
        mkdir($this->root . '/apps/archiq');
        file_put_contents($this->root . '/apps/archiq/archiq.app', Neon::encode([
            'name' => 'archiq', 'version' => '1.0.0', 'extends' => 'app://shinobi#1.0.0',
            'handler' => 'ArchiqPocHandler',
        ], true));
        file_put_contents($this->root . '/scrolls/configs/deployment.config', Neon::encode([
            'version' => '1.0.0', 'bindings' => [[
                'transport' => 'http', 'address' => '127.0.0.1', 'host' => ['localhost', 'archiq.test'],
                'port' => $this->port, 'app' => 'app://archiq#1.0.0',
            ]],
        ], true));
        $this->startRuntime('app://archiq', <<<'CODE'
class ArchiqPocHandler {
    public function __invoke(\Shinobi\HttpRequest $request): \Shinobi\HttpResponse {
        if ($request->path === '/fail') throw new \RuntimeException('private handler failure');
        return new \Shinobi\HttpResponse(201, ['content-type' => 'text/plain'], 'archiq:' . $request->method . ':' . $request->body);
    }
}
CODE);
        self::assertSame([201, 'archiq:POST:payload'], $this->request('/', 'archiq.test', 'POST', 'payload'));
        self::assertSame([201, 'archiq:POST:0'], $this->request('/', 'archiq.test', 'POST', '0'));
        [$status, $body] = $this->request('/fail', 'archiq.test');
        self::assertSame(500, $status);
        self::assertStringNotContainsString('private handler failure', $body);
        self::assertSame(201, $this->request('/after', 'archiq.test')[0]);
    }

    private function startRuntime(string $uri, string $handler = ''): void
    {
        $script = '<?php require ' . var_export(dirname(__DIR__) . '/lib/autoload.php', true) . ';' . $handler
            . '(new \\Shinobi\\Runtime\\Runtime(' . var_export($this->root, true) . '))->run(' . var_export($uri, true) . ');';
        file_put_contents($this->root . '/run.php', $script);
        $this->start([PHP_BINARY, $this->root . '/run.php']);
    }

    private function start(array $command, ?array $environment = null): void
    {
        $this->process = proc_open($command, [0 => ['file', '/dev/null', 'r'], 1 => ['file', $this->log, 'a'], 2 => ['file', $this->log, 'a']], $pipes, dirname(__DIR__), $environment);
        self::assertIsResource($this->process);
        $deadline = microtime(true) + 5;
        do {
            if (!proc_get_status($this->process)['running']) {
                self::fail('Runtime exited before listening: ' . file_get_contents($this->log));
            }
            $socket = @stream_socket_client('tcp://127.0.0.1:' . $this->port, $errno, $error, 0.1);
            if ($socket !== false) {
                fclose($socket);
                return;
            }
            usleep(20000);
        } while (microtime(true) < $deadline);
        self::fail('Runtime did not listen: ' . file_get_contents($this->log));
    }

    /** @return array{int, string} */
    private function request(string $path, string $host = 'localhost', string $method = 'GET', string $body = ''): array
    {
        $context = stream_context_create(['http' => [
            'method' => $method, 'header' => 'Host: ' . $host . "\r\nContent-Type: text/plain\r\n",
            'content' => $body, 'ignore_errors' => true, 'timeout' => 3,
        ]]);
        $result = file_get_contents('http://127.0.0.1:' . $this->port . $path, false, $context);
        self::assertNotFalse($result);
        $headers = http_get_last_response_headers();
        preg_match('/HTTP\/\S+ (\d+)/', $headers[0], $matches);
        return [(int) $matches[1], $result];
    }

    private function copyTree(string $source, string $destination): void
    {
        mkdir($destination);
        foreach (new \DirectoryIterator($source) as $entry) {
            if ($entry->isDot()) continue;
            $target = $destination . '/' . $entry->getFilename();
            if ($entry->isDir()) $this->copyTree($entry->getPathname(), $target);
            else copy($entry->getPathname(), $target);
        }
    }

    private function removeTree(string $path): void
    {
        foreach (new \DirectoryIterator($path) as $entry) {
            if ($entry->isDot()) continue;
            if ($entry->isDir()) $this->removeTree($entry->getPathname());
            else unlink($entry->getPathname());
        }
        rmdir($path);
    }
}
