<?php

declare(strict_types=1);

namespace Shinobi;

use RuntimeException;

final class ApplicationExecutor
{
    public function execute(Application $application, HttpRequest $request): HttpResponse
    {
        if ($application->handler === null) {
            return new HttpResponse(404, ['content-type' => 'text/plain; charset=utf-8'], 'Application has no HTTP handler.');
        }

        if (!class_exists($application->handler)) {
            throw new RuntimeException(sprintf('Application handler not found: %s.', $application->handler));
        }

        $handler = new $application->handler();
        if (!is_callable($handler)) {
            throw new RuntimeException(sprintf('Application handler is not invokable: %s.', $application->handler));
        }

        $response = $handler($request);
        if (!$response instanceof HttpResponse) {
            throw new RuntimeException(sprintf('Application handler must return %s.', HttpResponse::class));
        }

        return $response;
    }
}
