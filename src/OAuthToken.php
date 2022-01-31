<?php

namespace AuthManager;

class OAuthToken implements OAuthTokenInterface
{

    private string $accessToken;
    private string $tokenType;
    private string $expiresIn;
    private string $refreshToken;
    private array $scope;

    private string $error;
    private string $errorDescription;
    private string $errorUri;

    public function __construct(array $token)
    {
        $this->accessToken = empty($token['access_token']) ? '' : $token['access_token'];
        $this->tokenType = empty($token['token_type']) ? '' : $token['token_type'];
        $this->expiresIn = empty($token['expires_in']) ? '' : $token['expires_in'];
        $this->refreshToken = empty($token['refresh_token']) ? '' : $token['refresh_token'];
        $scope = empty($token['scope']) ? '' : $token['scope'];
        $this->scope = is_array($scope) ? $scope : explode(' ', $scope);

        $this->error = empty($token['error']) ? '' : $token['error'];
        $this->errorDescription = empty($token['error_description']) ? '' : $token['error_description'];
        $this->errorUri = empty($token['error_uri']) ? '' : $token['error_uri'];
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getScope(): array
    {
        return $this->scope;
    }

    public function hasError(): bool
    {
        return !!$this->error;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getErrorDescription(): string
    {
        return $this->errorDescription;
    }

    public function getErrorUri(): string
    {
        return $this->errorUri;
    }
}