<?php

namespace AuthManager\OAuthProviders\BattleNet;

class ProviderCN extends Provider
{
    public function __construct(string $id, string $secret, array $scope, string $redirectUri)
    {
        $this->region = 'cn';
        parent::__construct($id, $secret, $scope, $redirectUri);
    }
}