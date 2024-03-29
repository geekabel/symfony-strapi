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
        // TODO: Verifier si l'authentification est activé
        // if (!$this->getParameter('symfony_strapi.authentication.enabled')) {
        //     throw new \LogicException('Authentication is not enabled in the Symfony Strapi client configuration.');
        // }
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

    /**
     * @param array $filters
     * @return string
     */
    private function buildFilters(array $filters): string
    {
        $filterString = '';

        foreach ($filters as $field => $criteria) {
            foreach ($criteria as $operator => $value) {
                $filterString .= "&filters[{$field}][{$operator}]={$value}";
            }
        }

        return $filterString;
    }

    /**
     * Make a filtered GET request to retrieve a list of entries.
     * 
     *
     * @param string $pluralApiId
     * @param array $filters
     *
     * @return array
     *
     * @throws StrapiClientException
     */
    public function getEntriesWithFilters(string $pluralApiId, array $filters): array
    {
        $url = $this->apiUrl . "/api/{$pluralApiId}?{$this->buildFilters($filters)}";

        return $this->request('GET', $url);
    }


    /**
     * Make a sorted GET request to retrieve a list of entries.
     *
     * @param string $pluralApiId
     * @param string $sortField
     * @param string $sortOrder
     *
     * @return array
     *
     * @throws StrapiClientException
     */
    public function getEntriesWithSort(string $pluralApiId, string $sortField, string $sortOrder = 'asc'): array
    {
        $url = $this->apiUrl . "/api/{$pluralApiId}";

        $options = ['query' => ['sort' => "{$sortField}:{$sortOrder}"]];

        return $this->request('GET', $url, $options);
    }

    /**
     * Make a sorted GET request to retrieve a list of entries with multiple sorting fields.
     *
     * @param string $pluralApiId
     * @param array $sortFields
     *
     * @return array
     *
     * @throws StrapiClientException
     */
    public function getEntriesWithMultipleSort(string $pluralApiId, array $sortFields): array
    {
        $url = $this->apiUrl . "/api/{$pluralApiId}";

        $sortParams = [];

        foreach ($sortFields as $index => $sortField) {
            $sortParams[] = "sort[{$index}]={$sortField}";
        }

        $options = ['query' => $sortParams];

        return $this->request('GET', $url, $options);
    }


    /**
     * Make a paginated GET request to retrieve a list of entries by page.
     *
     * @param string $pluralApiId
     * @param int $page
     * @param int $pageSize
     *
     * @return array
     *
     * @throws StrapiClientException
     */
    public function getEntriesByPage(string $pluralApiId, int $page = 1, int $pageSize = 25): array
    {
        $url = $this->apiUrl . "/api/{$pluralApiId}";

        $options = ['query' => ['pagination[page]' => $page, 'pagination[pageSize]' => $pageSize]];

        return $this->request('GET', $url, $options);
    }

    /**
     * Make a paginated GET request to retrieve a list of entries by offset.
     *
     * @param string $pluralApiId
     * @param int $start
     * @param int $limit
     *
     * @return array
     *
     * @throws StrapiClientException
     */
    public function getEntriesByOffset(string $pluralApiId, int $start = 0, int $limit = 25): array
    {
        $url = $this->apiUrl . "/api/{$pluralApiId}";

        $options = ['query' => ['start' => $start, 'limit' => $limit]];

        return $this->request('GET', $url, $options);
    }
}