<?php

declare(strict_types=1);

namespace Shinobi\Applications;

use Shinobi\HttpRequest;
use Shinobi\HttpResponse;

final class Shinobi
{
    public function __invoke(HttpRequest $request): HttpResponse
    {
        return new HttpResponse(
            headers: ['content-type' => 'application/json; charset=utf-8'],
            body: json_encode([
                'name' => 'shinobi',
                'status' => 'ok',
                'method' => $request->method,
                'path' => $request->path,
            ], JSON_THROW_ON_ERROR),
        );
    }
}
