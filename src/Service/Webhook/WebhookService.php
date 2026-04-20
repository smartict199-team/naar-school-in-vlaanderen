<?php

declare(strict_types=1);

namespace App\Service\Webhook;

use App\Entity\School;
use App\Entity\SchoolYear;
use App\Entity\Webhook\WebhookEndpoint;
use App\Model\Webhook\SchoolUpdatesWebhookBody;
use App\Repository\Webhook\WebhookEndpointRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WebhookService
{
    private HttpClientInterface $client;
    private WebhookEndpointRepository $webhookEndpointRepository;
    private LoggerInterface $logger;

    public function __construct(
        HttpClientInterface $client,
        WebhookEndpointRepository $webhookEndpointRepository,
        LoggerInterface $logger
    ) {
        $this->client = $client;
        $this->webhookEndpointRepository = $webhookEndpointRepository;
        $this->logger = $logger;
    }

    public function publishUpdatedSchool(School $school, SchoolYear $schoolYear): void
    {
        $body = new SchoolUpdatesWebhookBody();
        $body->schoolId = $school->getId();
        $body->startYear = $schoolYear->getStartYear();
        $body->endYear = $schoolYear->getEndYear();
        $body->establishmentNumbers = $school->getEstablishmentNumbers();

        $this->postWebhooks($body);
    }

    private function postWebhooks(SchoolUpdatesWebhookBody $body): void
    {
        $webhookEndpoints = $this->webhookEndpointRepository->findAll();
        $options['body'] = json_encode($body);

        /** @var WebhookEndpoint $webhookEndpoint */
        foreach ($webhookEndpoints as $webhookEndpoint) {
            try {
                $this->client->request('POST', $webhookEndpoint->getEndpoint(), $options);
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }
    }
}
