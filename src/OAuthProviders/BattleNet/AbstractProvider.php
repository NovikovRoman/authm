<?php

namespace AuthManager\OAuthProviders\BattleNet;

use AuthManager\Exceptions\APIException;
use AuthManager\OAuthProviders\AbstractProvider as OAuthProvidersAbstractProvider;
use AuthManager\Parameters;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

abstract class AbstractProvider extends OAuthProvidersAbstractProvider
{
    const AUTHORIZE_URI = 'https://{region}.battle.net/oauth/authorize';
    const CH_AUTHORIZE_URI = 'https://www.battlenet.com.cn/oauth/authorize';
    const TOKEN_URI = 'https://{region}.battle.net/oauth/token';
    const CH_TOKEN_URI = 'https://www.battlenet.com.cn/oauth/token';

    const API_BASE_PATH = 'https://{region}.battle.net';
    const CN_API_BASE_PATH = 'https://www.battlenet.com.cn';

    protected $region = 'eu';

    protected function authorizeURL(string $region): string
    {
        return $region == 'cn'
            ? self::CH_AUTHORIZE_URI
            : str_replace('{region}', $region, self::AUTHORIZE_URI);
    }

    protected function tokenURL(string $region)
    {
        return $region == 'cn'
            ? self::CH_TOKEN_URI
            : str_replace('{region}', $region, self::TOKEN_URI);
    }

    protected function apiBasePath(string $region): string
    {
        return $region == 'cn'
            ? self::CN_API_BASE_PATH
            : str_replace('{region}', $region, self::API_BASE_PATH);
    }

    public function getAuthorizeURL(): string
    {
        return $this->authorizeURL($this->region);
    }

    public function getTokenUrl(): string
    {
        return $this->tokenURL($this->region);
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
        $headers = array_merge($headers, $this->getClientHeaders(), $this->getAuthHeaders());

        $url = $this->apiBasePath($this->region) . $path;
        if (!empty($query)) {
            $url .= stripos($path, '?') !== false ? '&' : '?';
            $url .= http_build_query($query);
        }

        try {
            $content = $this->httpClient->request(Parameters::METHOD_GET, $url, [
                'headers' => $headers,
                'connect_timeout' => Parameters::CONNECT_TIMEOUT,
            ])->getBody()->getContents();

            if ($resp = json_decode($content, true)) {
                return $resp;
            }

        } catch (RequestException $e) {
            throw $this->requestError($e);
        }

        throw $this->unknownError(new APIException('The body does not contain an array (' . $content . ')'));
    }

    /**
     * @param string $path
     * @param array $params
     * @param array $headers
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function requestPost(string $path, array $params, array $headers = []): array
    {
        $options = [
            'verify' => false,
            'connect_timeout' => Parameters::CONNECT_TIMEOUT,
            'headers' => array_merge($headers, $this->getClientHeaders(), $this->getAuthHeaders()),
            'body' => http_build_query($params),
        ];

        try {
            $content = $this->httpClient->request(
                Parameters::METHOD_POST, self::API_BASE_PATH . $path, $options)
                ->getBody()->getContents();

            if ($resp = json_decode($content, true)) {
                return $resp;
            }

        } catch (RequestException $e) {
            throw $this->requestError($e);
        }

        throw $this->unknownError(new APIException('The body does not contain an array (' . $content . ')'));
    }

    private function getClientHeaders(): array
    {
        return [
            'Client-ID' => $this->getClientID(),
        ];
    }

    private function requestError(RequestException $e): APIException
    {
        $resp = json_decode($e->getResponse()->getBody()->getContents(), true);
        if (!$resp) {
            return (new APIException($e->getMessage()))
                ->setStatus($e->getResponse()->getStatusCode())
                ->setStatusMessage($e->getResponse()->getReasonPhrase());
        }

        return (new APIException($resp['message']))
            ->setStatus($resp['status'])
            ->setStatusMessage($resp['error']);
    }
}