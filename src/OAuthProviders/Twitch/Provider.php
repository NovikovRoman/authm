<?php

namespace AuthManager\OAuthProviders\Twitch;

use AuthManager\Exceptions\APIException;
use AuthManager\OAuthProviderInterface;
use AuthManager\OAuthProviders\AbstractProvider;
use AuthManager\Constants;
use AuthManager\ProviderWithAPIInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class Provider extends AbstractProvider implements OAuthProviderInterface, ProviderWithAPIInterface
{
    const API_BASE_PATH = 'https://api.twitch.tv/helix';
    const TOKEN_URI = 'https://id.twitch.tv/oauth2/token';
    const AUTHORIZE_URI = 'https://id.twitch.tv/oauth2/authorize';

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
        $headers = array_merge($headers, $this->getClientHeaders(), $this->getAuthHeaders());

        $url = self::API_BASE_PATH . $path;
        if (stripos($path, '?') !== false && !empty($query)) {
            $url .= '&';

        } else if (!empty($query)) {
            $url .= '?';
        }

        $url .= empty($query) ? '' : http_build_query($query);

        try {
            $content = $this->httpClient->request(Constants::METHOD_GET, $url, [
                'connect_timeout' => Constants::CONNECT_TIMEOUT,
                'headers' => $headers,
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
            'connect_timeout' => Constants::CONNECT_TIMEOUT,
            'headers' => array_merge($headers, $this->getClientHeaders(), $this->getAuthHeaders()),
            'body' => http_build_query($params),
        ];

        try {
            $content = $this->httpClient->request(Constants::METHOD_POST, self::API_BASE_PATH . $path, $options)
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