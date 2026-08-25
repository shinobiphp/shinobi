<?php

declare(strict_types=1);

namespace Shinobi;

interface ApplicationRepository
{
    public function find(string $uri): ?Application;
}
