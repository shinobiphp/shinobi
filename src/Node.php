<?php

declare(strict_types=1);

namespace Shinobi;

use RuntimeException;

final class Node
{
    private ?object $server = null;

    public function __construct(
        private readonly Deployment $deployment,
        private readonly BindingResolver $bindings,
        private readonly ApplicationResolver $applications,
        private readonly ApplicationExecutor $executor,
    ) {
    }

    public function handle(HttpRequest $request): HttpResponse
    {
        $app = $this->bindings->resolve([
            'transport' => $request->transport,
            'host' => $request->host,
            'port' => $request->port,
        ]);

        if ($app === null) {
            return new HttpResponse(404, ['content-type' => 'text/plain; charset=utf-8'], 'No application is bound to this endpoint.');
        }

        return $this->executor->execute($this->applications->resolve($app), $request);
    }

    public function start(): void
    {
        if ($this->server !== null) {
            throw new RuntimeException('Shinobi node is already running.');
        }

        $binding = $this->deployment->bindings[0] ?? null;
        if (!is_array($binding) || !isset($binding['port'])) {
            throw new RuntimeException('No HTTP listener is configured.');
        }

        $port = $binding['port'];
        if (is_array($port)) {
            $port = $port[0] ?? null;
        }
        if (!is_int($port)) {
            throw new RuntimeException('HTTP listener port must be an integer.');
        }

        $address = isset($binding['address']) && is_string($binding['address'])
            ? $binding['address']
            : '0.0.0.0';

        $server = new \OpenSwoole\Http\Server($address, $port);
        $server->on('request', function (\OpenSwoole\Http\Request $request, \OpenSwoole\Http\Response $response): void {
            $host = $request->header['host'] ?? '';
            [$hostname, $port] = $this->endpoint($host);
            $result = $this->handle(new HttpRequest(
                transport: 'http',
                host: $hostname,
                port: $port,
                method: $request->server['request_method'] ?? 'GET',
                path: $request->server['request_uri'] ?? '/',
                headers: $request->header ?? [],
                body: $request->rawcontent() ?: '',
            ));

            $response->status($result->status);
            foreach ($result->headers as $name => $value) {
                $response->header($name, $value);
            }
            $response->end($result->body);
        });

        $this->server = $server;
        $server->start();
    }

    public function stop(): void
    {
        if ($this->server !== null && method_exists($this->server, 'shutdown')) {
            $this->server->shutdown();
        }

        $this->server = null;
    }

    /** @return array{0: string, 1: int} */
    private function endpoint(string $host): array
    {
        if ($host === '') {
            return ['', 80];
        }

        $parts = parse_url('http://' . $host);
        if (!is_array($parts) || !isset($parts['host'])) {
            return [$host, 80];
        }

        return [$parts['host'], $parts['port'] ?? 80];
    }
}
