<?php

namespace AuthManager\OAuthProviders;

use AuthManager\Exceptions\APIException;
use AuthManager\OAuthTokenInterface;
use AuthManager\Parameters;
use Exception;
use GuzzleHttp\Client;

class AbstractProvider
{
    protected $id;
    protected $secret;
    protected $redirectUri;
    /** @var OAuthTokenInterface */
    protected $token;
    protected $scope;
    protected $httpClient;

    public function __construct(string $id, string $secret, array $scope, string $redirectUri)
    {
        $this->id = $id;
        $this->secret = $secret;
        $this->scope = $scope;
        $this->redirectUri = $redirectUri;
        $this->httpClient = new Client(['timeout' => Parameters::TIMEOUT]);
    }

    public function setToken(OAuthTokenInterface $token)
    {
        $this->token = $token;
        return $this;
    }

    public function getClientID(): string
    {
        return $this->id;
    }

    public function getSecretKey(): string
    {
        return $this->secret;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function getScope(): array
    {
        return $this->scope;
    }

    public function getToken(): OAuthTokenInterface
    {
        return $this->token;
    }

    protected function getAuthHeaders(): array
    {
        return [
            'Authorization' => ucfirst($this->getToken()->getTokenType())
                . ' ' . $this->getToken()->getAccessToken(),
        ];
    }

    /**
     * @param Exception $e
     * @return APIException
     */
    protected function unknownError(Exception $e)
    {
        return (new APIException($e->getMessage()))
            ->setStatus(500)
            ->setStatusMessage('Internal Server Error');
    }
}