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
        $query = '';
        if (!empty($ids)) {
            $query = 'id=' . implode('&id=', $ids);
        }
        return $this->provider->requestGet('/videos', $query); // Client
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
        $query = 'user_id=' . $id;
        if (!empty($filter)) {
            $query .= '&' . http_build_query($filter);
        }
        return $this->provider->requestGet('/videos', $query); // Client
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
        $query = 'game_id=' . $id;
        if (!empty($filter)) {
            $query .= '&' . http_build_query($filter);
        }
        return $this->provider->requestGet('/videos', $query); // Client
    }
}