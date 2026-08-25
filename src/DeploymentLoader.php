<?php

declare(strict_types=1);

namespace Shinobi;

use Nette\Neon\Neon;
use RuntimeException;

final readonly class DeploymentLoader
{
    public function __construct(private string $path)
    {
    }

    public function load(): Deployment
    {
        if (!is_file($this->path)) {
            throw new RuntimeException(sprintf('Deployment scroll not found: %s', $this->path));
        }

        $definition = Neon::decodeFile($this->path);

        if (!is_array($definition)) {
            throw new RuntimeException('Deployment scroll must decode to an array.');
        }

        return Deployment::fromArray($definition);
    }
}
