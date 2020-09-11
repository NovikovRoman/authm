<?php

namespace AuthManager\OAuthProviders\Discord;

use AuthManager\Exceptions\APIException;
use GuzzleHttp\Exception\GuzzleException;

class User
{
    private $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function me()
    {
        return $this->provider->requestGet('/users/@me');
    }
}