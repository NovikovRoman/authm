<?php

namespace AuthManager\OpenIDProviders\Steam;

interface CategoryInterface
{
    public function __construct($apiKey);

    public static function categoryName(): string;
}