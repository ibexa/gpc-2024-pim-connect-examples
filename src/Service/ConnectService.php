<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\Service;

use AllowDynamicProperties;
use Http\Client\Exception;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Prototype\Connect\Service\SettingService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[AllowDynamicProperties] class ConnectService
{
    public function __construct(private HttpClientInterface $client, private LoggerInterface $logger, private SettingService $settingService, private ConfigResolverInterface $configResolver)
    {
        $this->initializeSerializer();
    }

    private function initializeSerializer()
    {
        $encoders = [new JsonEncoder()];
        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer(null, null, null, $extractor), new ArrayDenormalizer(), new GetSetMethodNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function isAlive(): bool
    {
        return true; //@TODO
    }

    public function checkProductAvailabilityByCode(string $productCode, int $amount): array
    {
        $payload = ['product_code' => $productCode, 'amount' => $amount];
        $jsonContent = $this->serializer->serialize($payload, JsonEncoder::FORMAT);

        $options = [
            'headers' => [
                'User-Agent' => 'Ibexa DXP',
                'Content-Type' => 'application/json',
                'Accept' => 'application/api.ProductAvailability+json',
            ],
            'body' => $jsonContent,
        ];

        $response = $this->handleApiRequest(Request::METHOD_POST, $options);
        $data = json_decode($response->getContent(), true);

        return $data;
    }

    public function handleRequest(string $method, array $payload, array $additionalHeaders): array
    {
        $jsonContent = $this->serializer->serialize($payload, JsonEncoder::FORMAT);

        $options = [
            'headers' => array_merge(
                [
                    'User-Agent' => 'Ibexa DXP',
                    'Content-Type' => 'application/json',
                ],
                $additionalHeaders
            ),
            'body' => $jsonContent,
        ];

        $response = $this->handleApiRequest($method, $options);
        $data = json_decode($response->getContent(), true);

        return $data;
    }

    private function handleApiRequest(string $method, array $options): ResponseInterface
    {
        $response = $this->client->request(
            $method,
            $this->getWebhookEndpointUrl(),
            $options
        );

        if ($response->getStatusCode() == Response::HTTP_OK && $response->getContent() == 'Accepted') {
            throw new \Exception('Scenario seems not to be active, please activate in Ibexa Connect');
        } elseif ($response->getStatusCode() == Response::HTTP_INTERNAL_SERVER_ERROR || $response->getStatusCode() == Response::HTTP_BAD_REQUEST) {
            throw new \Exception('Error within Ibexa Connect, please check logs');
        }

        return $response;
    }

    public function sendData(?Content $content, string $event, array $additionalData = []): void
    {
        $payload = [];
        $payload['event'] = $event;

        $payload['data'] = $additionalData;

        $options = [
            'json' => $payload,
            'headers' => [
                'User-Agent' => 'Ibexa DXP',
            ],
        ];
        try {
            $this->logger->info(sprintf('Sending data from event %s to Ibexa Connect webhook %s', $event, $this->getWebhookEndpointUrl()));
            $response = $this->client->request('POST', $this->getWebhookEndpointUrl(), $options);

            if ($response->getStatusCode() == Response::HTTP_OK) {
                $this->logger->info(sprintf('Payload submission to %s successful with status code %d ', $this->getWebhookEndpointUrl(), $response->getStatusCode()));
            } elseif ($response->getStatusCode() == Response::HTTP_NOT_FOUND) {
                $this->logger->error(sprintf('Webhook %s is not available or could not be found.', $this->getWebhookEndpointUrl()));
            } else {
                $this->logger->error(sprintf('Payload submission to %s returned with error code %d', $this->getWebhookEndpointUrl(), $response->getStatusCode()));
            }
        } catch (Exception $e) {
            $this->logger->critical(sprintf('Payload submission to %s caused exception %s', $this->getWebhookEndpointUrl(), $e->getMessage()));
        }
    }

    private function getWebhookEndpointUrl(): string
    {
        return $this->settingService->getEndpointUrl();
    }
}
