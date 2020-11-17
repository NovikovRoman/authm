<?php

namespace AuthManager\OAuthProviders\Twitch;

use AuthManager\Exceptions\APIException;
use GuzzleHttp\Exception\GuzzleException;

class Videos
{
    private $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param array $ids
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function getID(array $ids = [])
    {
        $path = '/videos';
        if (!empty($ids)) {
            $path .= 'id=' . implode('&id=', $ids);
        }
        return $this->provider->requestGet($path); // Client
    }

    /**
     * @param $id
     * @param array $filter
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function getUserID($id, $filter = [])
    {
        $path = '/videos?user_id=' . $id;
        return $this->provider->requestGet($path, $filter); // Client
    }

    /**
     * @param $id
     * @param array $filter
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function getGameID($id, $filter = [])
    {
        $path = '/videos?game_id=' . $id;
        return $this->provider->requestGet($path, $filter); // Client
    }
}