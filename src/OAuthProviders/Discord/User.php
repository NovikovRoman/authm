<?php

namespace AuthManager\OAuthProviders\Discord;

use AuthManager\Exceptions\APIException;
use GuzzleHttp\Exception\GuzzleException;

class User
{
    private Provider $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function me(): array
    {
        return $this->provider->requestGet('/users/@me');
    }
}