<?php

namespace AuthManager\OAuthProviders\Discord;

use AuthManager\Exceptions\APIException;
use AuthManager\OAuthProviderInterface;
use AuthManager\OAuthProviders\AbstractProvider;
use AuthManager\Parameters;
use AuthManager\ProviderWithAPIInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class Provider extends AbstractProvider implements OAuthProviderInterface, ProviderWithAPIInterface
{
    const API_BASE_PATH = 'https://discordapp.com/api/v6';
    const TOKEN_URI = 'https://discordapp.com/api/v6/oauth2/token';
    const AUTHORIZE_URI = 'https://discordapp.com/api/oauth2/authorize';

    public function getAuthorizeURL(): string
    {
        return self::AUTHORIZE_URI;
    }

    public function getTokenUrl(): string
    {
        return self::TOKEN_URI;
    }

    /**
     * @param string $path
     * @param array $query
     * @param array $headers
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function requestGet(string $path, array $query = [], array $headers = []): array
    {
        $url = self::API_BASE_PATH . $path . (empty($query) ? '' : '?' . http_build_query($query));
        try {
            $content = $this->httpClient->request(Parameters::METHOD_GET, $url, [
                'connect_timeout' => Parameters::CONNECT_TIMEOUT,
                'headers' => array_merge($headers, $this->getAuthHeaders()),
            ])->getBody()->getContents();
            $resp = json_decode($content, true);

        } catch (RequestException $e) {
            throw $this->requestError($e);
        }

        return $resp;
    }

    /**
     * @param $path
     * @param $params
     * @param array $headers
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function requestPost(string $path, array $params, array $headers = []): array
    {
        $options = [
            'connect_timeout' => Parameters::CONNECT_TIMEOUT,
            'verify' => false,
            'headers' => array_merge($headers, $this->getAuthHeaders()),
            'body' => http_build_query($params),
        ];
        try {
            $content = $this->httpClient
                ->request(Parameters::METHOD_POST, self::API_BASE_PATH . $path, $options)
                ->getBody()->getContents();
            $resp = json_decode($content, true);

        } catch (RequestException $e) {
            throw $this->requestError($e);
        }

        return $resp;
    }

    /**
     * @param RequestException $e
     * @return APIException
     */
    private function requestError(RequestException $e)
    {
        $resp = json_decode($e->getResponse()->getBody()->getContents(), true);
        if (!$resp) {
            return (new APIException($e->getMessage()))
                ->setStatus($e->getResponse()->getStatusCode())
                ->setStatusMessage($e->getResponse()->getReasonPhrase());
        }

        return (new APIException($resp['code']))
            ->setStatus($e->getResponse()->getStatusCode())
            ->setStatusMessage($e->getResponse()->getReasonPhrase());
    }
}