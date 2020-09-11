<?php

namespace AuthManager\OpenIDProviders\Steam;

use AuthManager\OpenIDInterface;

class Provider implements OpenIDInterface
{
    const AUTH_URI = 'https://steamcommunity.com/openid/login';

    public function getAuthURI(): string
    {
        return self::AUTH_URI;
    }
}