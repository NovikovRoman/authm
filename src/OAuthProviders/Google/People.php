<?php

namespace AuthManager\OAuthProviders\Google;

use AuthManager\Exceptions\APIException;
use GuzzleHttp\Exception\GuzzleException;

class People
{
    const SERVICE = 'people';
    private Provider $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param array $personFields
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function me(array $personFields = ['emailAddresses']): array
    {
        $path = str_replace('{service}', self::SERVICE,
            Provider::API_BASE_PATH . '/v1/people/me');
        return $this->provider->requestGet($path, ['personFields' => implode(',', $personFields)]);
    }
}