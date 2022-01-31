<?php

namespace AuthManager;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;

class OAuthManager implements OAuthManagerInterface
{
    private OAuthProviderInterface $provider;
    private Client $httpClient;

    public function __construct(OAuthProviderInterface $provider)
    {
        $this->provider = $provider;
        $this->httpClient = new Client(['timeout' => Constants::TIMEOUT]);
    }

    /**
     * @param string $state
     * @param bool $redirect
     * @param array $params
     * @return string
     */
    public function signin(string $state, bool $redirect = false, array $params = []): string
    {
        $query = [
            'response_type' => 'code',
            'redirect_uri' => $this->provider->getRedirectUri(),
            'client_id' => $this->provider->getClientID(),
            'scope' => implode(' ', $this->provider->getScope()),
            'state' => $state,
        ];

        $query = array_merge($query, $params);

        $url = $this->provider->getAuthorizeURL() . '?' . http_build_query($query, '', '&');
        if ($redirect) {
            header('Location: ' . $url);
            return '';
        }
        return $url;
    }

    /**
     * @param string $url
     * @param string $state
     * @return OAuthTokenInterface
     * @throws GuzzleException
     */
    public function getToken(string $url, string $state): OAuthTokenInterface
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $params);

        $headers = [
            'verify' => false,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $body = http_build_query([
            'client_id' => $this->provider->getClientID(),
            'client_secret' => $this->provider->getSecretKey(),
            'grant_type' => 'authorization_code',
            'code' => $params['code'],
            'redirect_uri' => $this->provider->getRedirectUri(),
            'scope' => implode(' ', $this->provider->getScope()),
        ]);

        $request = new Request('POST', $this->provider->getTokenUrl(), $headers, $body);
        $response = $this->httpClient->send($request);

        $json = json_decode($response->getBody()->getContents(), true);
        if (empty($json)) {
            throw new BadResponseException('Empty response', $request, $response);
        }

        $token = new OAuthToken($json);
        $this->provider->setToken($token);
        return $token;
    }
}