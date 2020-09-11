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
     * @param $after
     * @param $first
     * @param $fromID
     * @param $toID
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function follows($after, $first, $fromID, $toID)
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
    public function getID(array $ids = [])
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
    public function getLogin(array $logins = [])
    {
        $path = '/users';
        if (!empty($logins)) {
            $path .= '?login=' . implode('&login=', $logins);
        }
        return $this->provider->requestGet($path); // Auth
    }
}