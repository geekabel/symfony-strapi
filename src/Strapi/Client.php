<?php

namespace Geekabel\SymfonyStrapi\Strapi;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Geekabel\SymfonyStrapi\Exception\StrapiClientException;
use Symfony\Component\HttpClient\Exception\ClientException;

class Client implements ClientInterface
{
    private $httpClient;
    private string $apiUrl;
    private string $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $apiUrl, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }

    // Implement your Strapi API interaction methods here, including authentication
    private function request(string $method, string $url, array $options = []): array
    {
        try {
            $response = $this->httpClient->request($method, $url, $options);

            return $response->toArray();
        } catch (ClientException $e) {
            // Handle specific exceptions for client errors (4xx)
            throw StrapiClientException::createFromClientException($e);
        } catch (\Exception $e) {
            // Handle other exceptions
            throw new StrapiClientException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getEntries(string $pluralApiId): array
    {
        $url = $this->apiUrl . "/api/{$pluralApiId}";

        return $this->request('GET', $url);
    }

    public function createEntry(string $pluralApiId, array $data): array
    {
        $url = $this->apiUrl . "/api/{$pluralApiId}";

        return $this->request('POST', $url, ['json' => $data]);
    }

    public function getEntry(string $pluralApiId, string $documentId): array
    {
        $url = $this->apiUrl . "/api/{$pluralApiId}/{$documentId}";

        return $this->request('GET', $url);
    }

    public function updateEntry(string $pluralApiId, string $documentId, array $data): array
    {
        $url = $this->apiUrl . "/api/{$pluralApiId}/{$documentId}";

        return $this->request('PUT', $url, ['json' => $data]);
    }

    public function deleteEntry(string $pluralApiId, string $documentId): void
    {
        $url = $this->apiUrl . "/api/{$pluralApiId}/{$documentId}";

        $this->request('DELETE', $url);
    }

    public function authenticate(string $identifier, string $password): array
    {
        if (!$this->getParameter('symfony_strapi.authentication.enabled')) {
            throw new \LogicException('Authentication is not enabled in the Symfony Strapi client configuration.');
        }
        $url = $this->apiUrl . '/auth/local';

        $authData = [
            'identifier' => $identifier,
            'password' => $password,
        ];

        $options = [
            'json' => $authData,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];

        return $this->request('POST', $url, $options);
    }
}