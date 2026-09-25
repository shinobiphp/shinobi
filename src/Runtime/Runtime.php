<?php
declare(strict_types=1);
namespace Shinobi\Runtime;
use Codejitsu\Apps\ApplicationResolver;
use Codejitsu\Apps\EffectiveApplication;
use Codejitsu\Scrolls\ScrollCodex;
use Shinobi\BindingResolver;
use Shinobi\DeploymentLoader;
use Shinobi\Deployment;
use Shinobi\Node;
final readonly class Runtime
{
    public function __construct(private string $root, private ?string $host = null, private ?int $port = null) {}
    public function resolve(string $uri): EffectiveApplication
    {
        return (new ApplicationResolver($this->codex()))->resolve($uri);
    }
    public function run(string $uri): void
    {
        $codex = $this->codex();
        $application = (new ApplicationResolver($codex))->resolve($uri);
        $deployment = (new DeploymentLoader($this->root . '/scrolls/configs/deployment.config'))->load();
        $bindings = $deployment->bindings;
        foreach ($bindings as &$binding) {
            if (($binding['transport'] ?? null) !== 'http') {
                continue;
            }
            if ($this->host !== null) {
                $binding['address'] = $this->host;
            }
            if ($this->port !== null) {
                $binding['port'] = $this->port;
            }
        }
        unset($binding);
        $deployment = new Deployment($bindings);
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
