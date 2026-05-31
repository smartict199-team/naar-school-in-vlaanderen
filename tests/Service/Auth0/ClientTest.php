<?php

declare(strict_types=1);

namespace App\Tests\Service\Auth0;

use App\Service\Auth0\Client;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

class ClientTest extends TestCase
{
    public function testPostUsesConfiguredPasswordResetClientId(): void
    {
        $history = [];
        $historyMiddleware = Middleware::history($history);
        $mockHandler = new MockHandler([
            new Response(200, [], '{"access_token":"management-access-token"}'),
            new Response(200, [], ''),
        ]);
        $handlerStack = HandlerStack::create($mockHandler);
        $handlerStack->push($historyMiddleware);

        $client = new Client(
            'management-client-id',
            'management-client-secret',
            'https://api.example.com',
            'application-client-id',
            new HttpClient(['handler' => $handlerStack]),
            new Serializer([], [])
        );

        $client->post('/dbconnections/change_password', null, [
            'body' => [
                'email' => 'new.user@example.com',
                'connection' => 'Naarschoolin',
            ],
        ], true);

        self::assertCount(2, $history);
        $passwordResetRequestBody = json_decode((string) $history[1]['request']->getBody(), true);
        self::assertSame('application-client-id', $passwordResetRequestBody['client_id']);
    }
}
