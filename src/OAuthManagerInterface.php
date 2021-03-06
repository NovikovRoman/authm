<?php

namespace AuthManager;

interface OAuthManagerInterface
{
    public function __construct(OAuthProviderInterface $client);

    public function signin($state, $redirect = false, array $params = []): string;

    public function getToken(string $url, string $state): OAuthTokenInterface;

}