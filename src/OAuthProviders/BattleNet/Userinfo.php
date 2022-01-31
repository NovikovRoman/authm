<?php

namespace AuthManager\OAuthProviders\BattleNet;

use AuthManager\Exceptions\APIException;
use GuzzleHttp\Exception\GuzzleException;

class Userinfo
{
    private $provider;

    public function __construct(AbstractProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @throws GuzzleException
     * @throws APIException
     */
    public function get(): array
    {
        return $this->provider->requestGet('/oauth/userinfo'); // Client
    }
}