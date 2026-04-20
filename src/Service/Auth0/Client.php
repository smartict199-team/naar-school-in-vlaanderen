<?php

declare(strict_types=1);

namespace App\Service\Auth0;

use App\Model\Request\RequestInterface;
use App\Model\Response\ResponseInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class Client
{
    private \GuzzleHttp\Client $client;
    private string $clientToken;
    private string $secret;
    private string $audience;
    private SerializerInterface $serializer;

    public function __construct(string $clientToken, string $secret, string $audience, \GuzzleHttp\Client $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->clientToken = $clientToken;
        $this->secret = $secret;
        $this->serializer = $serializer;
        $this->audience = $audience;
    }

    /**
     * @param string[][] $options
     *
     * @return ResponseInterface|ResponseInterface[]|null
     */
    public function request(string $method, string $uri, string $class, array $options = [])
    {
        $headers = [
            'content-type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $this->getAccessToken()),
        ];

        if (\array_key_exists('headers', $options)) {
            $options = array_merge($options['headers'], $headers);
        } else {
            $options['headers'] = $headers;
        }

        $data = $this->client->request($method, $uri, $options);
        $body = $data->getBody()->getContents();

        return $this->serializer->deserialize($body, $class, 'json');
    }

    /**
     * @param string[][] $options
     */
    public function post(string $uri, ?RequestInterface $model, array $options = [], bool $requiresClientId = false): void
    {
        $headers = [
            'content-type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $this->getAccessToken()),
        ];

        if (\array_key_exists('headers', $options)) {
            $options = array_merge($options['headers'], $headers);
        } else {
            $options['headers'] = $headers;
        }

        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $serializer = new Serializer([$normalizer], [new JsonEncoder()]);

        if ($model instanceof RequestInterface) {
            $body = $normalizer->normalize($model);
            $options['body'] = $body;
        }

        if ($requiresClientId) {
            $options['body']['client_id'] = $this->clientToken;
        }

        $options['body'] = $serializer->serialize($options['body'], 'json');

        $this->client->request('post', $uri, $options);
    }

    /**
     * @param string[][] $options
     */
    public function patch(string $uri, ?RequestInterface $model, array $options = [], bool $requiresClientId = false): void
    {
        $headers = [
            'content-type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $this->getAccessToken()),
        ];

        if (\array_key_exists('headers', $options)) {
            $options = array_merge($options['headers'], $headers);
        } else {
            $options['headers'] = $headers;
        }

        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $serializer = new Serializer([$normalizer], [new JsonEncoder()]);

        if ($model instanceof RequestInterface) {
            $body = $normalizer->normalize($model);
            $options['body'] = $body;
        }

        if ($requiresClientId) {
            $options['body']['client_id'] = $this->clientToken;
        }

        $options['body'] = $serializer->serialize($options['body'], 'json');

        $this->client->request('patch', $uri, $options);
    }

    /**
     * @param string[][] $options
     */
    public function delete(string $uri, array $options = []): void
    {
        $headers = [
            'content-type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $this->getAccessToken()),
        ];

        if (\array_key_exists('headers', $options)) {
            $options = array_merge($options['headers'], $headers);
        } else {
            $options['headers'] = $headers;
        }

        $this->client->request('delete', $uri, $options);
    }

    private function getAccessToken(): string
    {
        $headers = [
            'content-type' => 'application/x-www-form-urlencoded',
        ];

        $data = $this->client->request('POST', '/oauth/token', [
            'headers' => $headers,
            'form_params' => [
                'client_id' => $this->clientToken,
                'client_secret' => $this->secret,
                'grant_type' => 'client_credentials',
                'audience' => $this->audience,
            ],
        ]);

        $data = json_decode($data->getBody()->getContents());

        return $data->access_token;
    }
}
