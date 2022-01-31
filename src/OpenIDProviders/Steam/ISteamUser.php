<?php

namespace AuthManager\OpenIDProviders\Steam;

use GuzzleHttp\Exception\GuzzleException;

class ISteamUser extends Category implements CategoryInterface
{
    const category = '/ISteamUser';

    public function __construct($apiKey)
    {
        parent::__construct($apiKey);
    }

    public static function categoryName(): string
    {
        return self::category;
    }

    /**
     * @param $id
     * @return array
     * @throws GuzzleException
     */
    public function getPlayerSummaries($id): array
    {
        $url = $this->buildUrl(self::categoryName(), '/GetPlayerSummaries/v0002/?key={key}&steamids=' . $id);
        $data = $this->get($url);
        return empty($data['players']) ? [] : $data['players'][0];
    }
}