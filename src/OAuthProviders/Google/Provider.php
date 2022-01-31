<?php

namespace AuthManager\OAuthProviders\Google;

use AuthManager\Exceptions\APIException;
use AuthManager\OAuthProviderInterface;
use AuthManager\OAuthProviders\AbstractProvider;
use AuthManager\Parameters;
use AuthManager\ProviderWithAPIInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class Provider extends AbstractProvider implements OAuthProviderInterface, ProviderWithAPIInterface
{
    const API_BASE_PATH = 'https://{service}.googleapis.com';
    const TOKEN_URI = 'https://oauth2.googleapis.com/token';
    const AUTHORIZE_URI = 'https://accounts.google.com/o/oauth2/v2/auth';

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
        $url = $path . (empty($query) ? '' : '?' . http_build_query($query));
        try {
            $content = $this->httpClient->request(Parameters::METHOD_GET, $url, [
                'headers' => array_merge($headers, $this->getAuthHeaders()),
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
     * todo: not used
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
            'connect_timeout' => Parameters::CONNECT_TIMEOUT,
            'verify' => false,
            'headers' => array_merge($headers, $this->getAuthHeaders()),
            'body' => http_build_query($params),
        ];
        try {
            $content = $this->httpClient
                ->request(Parameters::METHOD_POST, $path, $options)
                ->getBody()->getContents();

            if ($resp = json_decode($content, true)) {
                return $resp;
            }

        } catch (RequestException $e) {
            throw $this->requestError($e);
        }

        throw $this->unknownError(new APIException('The body does not contain an array (' . $content . ')'));
    }

    /**
     * @param RequestException $e
     * @return APIException
     */
    private function requestError(RequestException $e): APIException
    {
        $resp = json_decode($e->getResponse()->getBody()->getContents(), true);
        if (!$resp) {
            return (new APIException($e->getMessage()))
                ->setStatus($e->getResponse()->getStatusCode())
                ->setStatusMessage($e->getResponse()->getReasonPhrase());
        }

        return (new APIException($resp['error']['message']))
            ->setStatus($resp['error']['code'])
            ->setStatusMessage($resp['error']['status']);
    }
}