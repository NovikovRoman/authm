<?php

namespace AuthManager\OpenIDProviders\Steam;

use AuthManager\Parameters;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

abstract class Category
{
    const baseUrl = 'https://api.steampowered.com';
    protected $apiKey;
    /** @var Client */
    protected $httpClient;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = new Client(['timeout' => Parameters::TIMEOUT]);
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
    protected function get($url)
    {
        $response = $this->httpClient->request(Parameters::METHOD_GET, $url, [
            'connect_timeout' => Parameters::CONNECT_TIMEOUT,
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return empty($data['response']) ? [] : $data['response'];
    }
}