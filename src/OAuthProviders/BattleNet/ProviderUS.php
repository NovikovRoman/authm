<?php

namespace AuthManager\OAuthProviders\BattleNet;

class ProviderUS extends Provider
{
    public function __construct(string $id, string $secret, array $scope, string $redirectUri)
    {
        $this->region = 'us';
        parent::__construct($id, $secret, $scope, $redirectUri);
    }
}