<?php

declare(strict_types=1);

namespace Shinobi\Commands;

use Nette\Neon\Neon;
use Shinobi\FilesystemApplicationRepository;

final class AppsList
{
    public function __invoke(): string
    {
        $root = defined('SHINOBI_ROOT') ? SHINOBI_ROOT : getcwd();
        $path = rtrim($root, '/\\') . '/apps';
        $applications = [];

        foreach (glob($path . '/*/*.app') ?: [] as $manifest) {
            $name = basename($manifest, '.app');
            $application = new FilesystemApplicationRepository($path)->find('app://' . $name);
            if ($application !== null) {
                $applications[] = [
                    'name' => $application->name,
                    'extends' => $application->parent,
                    'handler' => $application->handler,
                ];
            }
        }

        return Neon::encode(['applications' => $applications]);
    }
}
