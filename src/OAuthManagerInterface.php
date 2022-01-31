<?php

namespace AuthManager;

interface OAuthManagerInterface
{
    public function __construct(OAuthProviderInterface $client);

    public function signin(string $state, bool $redirect = false, array $params = []): string;

    public function getToken(string $url, string $state): OAuthTokenInterface;

}