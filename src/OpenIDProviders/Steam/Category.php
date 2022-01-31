<?php

namespace AuthManager\OpenIDProviders\Steam;

use AuthManager\Constants;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

abstract class Category
{
    const baseUrl = 'https://api.steampowered.com';
    protected string $apiKey;
    protected Client $httpClient;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = new Client(['timeout' => Constants::TIMEOUT]);
    }

    protected function buildUrl($category, $url)
    {
        $url = self::baseUrl . $category . $url;
        return str_replace('{key}', $this->apiKey, $url);
    }

    /**
     * @param $url
     * @return array
     * @throws GuzzleException
     */
    protected function get($url): array
    {
        $response = $this->httpClient->request(Constants::METHOD_GET, $url, [
            'connect_timeout' => Constants::CONNECT_TIMEOUT,
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return empty($data['response']) ? [] : $data['response'];
    }
}