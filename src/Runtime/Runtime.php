<?php
declare(strict_types=1);
namespace Shinobi\Runtime;
use Codejitsu\Apps\ApplicationResolver;
use Codejitsu\Apps\EffectiveApplication;
use Codejitsu\Scrolls\ScrollCodex;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;
final readonly class Runtime
{
    public function __construct(private string $root, private string $host = '127.0.0.1', private int $port = 9501) {}
    public function resolve(string $uri): EffectiveApplication
    {
        $codex = new ScrollCodex();
        $codex->load($this->root . '/scrolls', 'shinobi');
        $codex->load($this->root . '/apps', 'apps');
        return (new ApplicationResolver($codex))->resolve($uri);
    }
    public function run(string $uri): void
    {
        $app = $this->resolve($uri);
        $server = new Server($this->host, $this->port);
        $server->on('start', static function () use ($app): void {
            fwrite(STDOUT, sprintf("Shinobi running %s\n", $app->uri));
        });
        $server->on('request', static function (Request $request, Response $response) use ($app): void {
            $response->header('content-type', 'application/json');
            $response->end(json_encode([
                'ok' => true,
                'app' => $app->uri,
                'spec' => $app->spec() ?? 'spec://shinobi/app',
                'inheritance' => $app->inheritance,
                'path' => $request->server['request_uri'] ?? '/',
            ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        });
        $server->start();
    }
}
