<?php

namespace AuthManager\OAuthProviders\BattleNet;

class ProviderAPAC extends Provider
{
    public function __construct(string $id, string $secret, array $scope, string $redirectUri)
    {
        $this->region = 'apac';
        parent::__construct($id, $secret, $scope, $redirectUri);
    }
}