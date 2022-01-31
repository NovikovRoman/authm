<?php

namespace AuthManager\OAuthProviders\Twitch;

use AuthManager\Exceptions\APIException;
use GuzzleHttp\Exception\GuzzleException;

class Users
{
    private $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param string $after
     * @param int $first
     * @param string $fromID
     * @param string $toID
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function follows(string $after, int $first, string $fromID, string $toID): array
    {
        $query = [];
        if ($after) {
            $query['after'] = $after;
        }
        if ($first) {
            $query['first'] = $first;
        }
        if ($fromID) {
            $query['from_id'] = $fromID;
        }
        if ($toID) {
            $query['to_id'] = $toID;
        }
        return $this->provider->requestGet('/users/follows', $query); // Client
    }

    /**
     * @param array $ids
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function getID(array $ids = []): array
    {
        $path = '/users';
        if (!empty($ids)) {
            $path .= '?id=' . implode('&id=', $ids);
        }
        return $this->provider->requestGet($path); // Auth
    }

    /**
     * @param array $logins
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function getLogin(array $logins = []): array
    {
        $path = '/users';
        if (!empty($logins)) {
            $path .= '?login=' . implode('&login=', $logins);
        }
        return $this->provider->requestGet($path); // Auth
    }
}