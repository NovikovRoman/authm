<?php

namespace AuthManager\OAuthProviders\Twitch;

use AuthManager\Exceptions\APIException;
use GuzzleHttp\Exception\GuzzleException;

class Videos
{
    private Provider $provider;

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
    public function getID(array $ids = []): array
    {
        $path = '/videos';
        if (!empty($ids)) {
            $path .= 'id=' . implode('&id=', $ids);
        }
        return $this->provider->requestGet($path); // Client
    }

    /**
     * @param string $id
     * @param array $filter
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function getUserID(string $id, array $filter = []): array
    {
        $path = '/videos?user_id=' . $id;
        return $this->provider->requestGet($path, $filter); // Client
    }

    /**
     * @param string $id
     * @param array $filter
     * @return array
     * @throws APIException
     * @throws GuzzleException
     */
    public function getGameID(string $id, array $filter = []): array
    {
        $path = '/videos?game_id=' . $id;
        return $this->provider->requestGet($path, $filter); // Client
    }
}