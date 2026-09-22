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
        return (new ApplicationResolver($this->codex()))->resolve($uri);
    }
    public function run(string $uri): void
    {
        $codex = $this->codex();
        $application = (new ApplicationResolver($codex))->resolve($uri);
        $deployment = (new DeploymentLoader($this->root . '/scrolls/configs/deployment.config'))->load();
        $node = new Node($deployment, new BindingResolver($deployment->bindings), new ApplicationResolver($codex));
        fwrite(STDOUT, sprintf("Shinobi running %s\n", $application->uri));
        $node->start();
    }

    private function codex(): ScrollCodex
    {
        $codex = new ScrollCodex();
        $codex->load($this->root . '/scrolls', 'shinobi');
        $codex->load($this->root . '/apps', 'apps');
        return $codex;
    }
}
