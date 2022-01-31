<?php

namespace AuthManager\OAuthProviders\BattleNet;

class ProviderEU extends Provider
{
    public function __construct(string $id, string $secret, array $scope, string $redirectUri)
    {
        $this->region = 'eu';
        parent::__construct($id, $secret, $scope, $redirectUri);
    }
}