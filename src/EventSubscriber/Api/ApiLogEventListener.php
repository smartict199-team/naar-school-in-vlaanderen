<?php

declare(strict_types=1);

namespace App\EventSubscriber\Api;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ApiLogEventListener
{
    private LoggerInterface $apiLogger;

    public function __construct(
        LoggerInterface $apiLogger
    ) {
        $this->apiLogger = $apiLogger;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $route = $request->get('_route');

        if(is_null($request->get('_route'))) {
            return;
        }

        if (!str_starts_with($request->get('_route'), 'api_')) {
            return;
        }

        $response = $event->getResponse();

        $this->apiLogger->info($route, [
            'path' => $request->getPathInfo(),
            'request_body' => $request->getContent(),
            'response' => $response->getContent(),
        ]);
    }
}
