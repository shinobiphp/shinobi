<?php

declare(strict_types=1);

namespace Shinobi;

use RuntimeException;
use Codejitsu\Apps\ApplicationResolver as CodejitsuApplicationResolver;

final class Node
{
    private ?object $server = null;

    public function __construct(
        private readonly Deployment $deployment,
        private readonly BindingResolver $bindings,
        private readonly CodejitsuApplicationResolver $applications,
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

        $application = $this->applications->resolve($app);
        $handler = $application->data['handler'] ?? null;
        if (!is_string($handler) || trim($handler) === '') {
            return new HttpResponse(404, ['content-type' => 'text/plain; charset=utf-8'], 'Application has no HTTP handler.');
        }
        if (!class_exists($handler)) throw new RuntimeException(sprintf('Application handler not found: %s.', $handler));
        $callable = new $handler();
        if (!is_callable($callable)) throw new RuntimeException(sprintf('Application handler is not invokable: %s.', $handler));
        $response = $callable($request);
        if (!$response instanceof HttpResponse) throw new RuntimeException(sprintf('Application handler must return %s.', HttpResponse::class));
        return $response;
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
        $server->on('request', function (\OpenSwoole\Http\Request $request, \OpenSwoole\Http\Response $response) use ($port): void {
            $host = $request->header['host'] ?? '';
            $hostname = parse_url('http://' . $host, PHP_URL_HOST);
            $hostname = is_string($hostname) ? $hostname : '';
            $body = $request->rawcontent();
            try {
                $result = $this->handle(new HttpRequest(
                    transport: 'http',
                    host: $hostname,
                    port: $port,
                    method: $request->server['request_method'] ?? 'GET',
                    path: $request->server['request_uri'] ?? '/',
                    headers: $request->header ?? [],
                    body: is_string($body) ? $body : '',
                ));
            } catch (\Throwable $error) {
                error_log((string) $error);
                $result = new HttpResponse(500, ['content-type' => 'text/plain; charset=utf-8'], 'Internal Server Error');
            }

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
}
