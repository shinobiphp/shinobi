<?php

declare(strict_types=1);

namespace Shinobi\Commands;

use Nette\Neon\Neon;
use RuntimeException;
use Shinobi\DeploymentLoader;

final class NodeInfo
{
    public function __invoke(): string
    {
        $root = defined('SHINOBI_ROOT') ? SHINOBI_ROOT : getcwd();
        $path = $root . '/scrolls/configs/deployment.config';
        if (!is_file($path)) {
            throw new RuntimeException(sprintf('Deployment scroll not found: %s.', $path));
        }

        $deployment = new DeploymentLoader($path)->load();

        return Neon::encode([
            'status' => 'configured',
            'bindings' => $deployment->bindings,
        ]);
    }
}
