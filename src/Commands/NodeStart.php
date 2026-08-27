<?php

declare(strict_types=1);

namespace Shinobi\Commands;

use Shinobi\ApplicationExecutor;
use Shinobi\ApplicationResolver;
use Shinobi\BindingResolver;
use Shinobi\DeploymentLoader;
use Shinobi\FilesystemApplicationRepository;
use Shinobi\Node;

final class NodeStart
{
    public function __invoke(): int
    {
        $root = defined('SHINOBI_ROOT') ? SHINOBI_ROOT : getcwd();
        $deployment = new DeploymentLoader($root . '/scrolls/configs/deployment.config')->load();
        $applications = new ApplicationResolver(new FilesystemApplicationRepository($root . '/apps'));
        $node = new Node($deployment, new BindingResolver($deployment->bindings), $applications, new ApplicationExecutor());

        $node->start();

        return 0;
    }
}
