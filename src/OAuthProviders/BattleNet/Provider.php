<?php

namespace AuthManager\OAuthProviders\BattleNet;

use AuthManager\OAuthProviderInterface;
use AuthManager\ProviderWithAPIInterface;

class Provider extends AbstractProvider implements OAuthProviderInterface, ProviderWithAPIInterface
{
    protected string $region = 'eu';
}