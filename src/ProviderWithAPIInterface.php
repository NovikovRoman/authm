<?php

namespace AuthManager;

interface ProviderWithAPIInterface
{
    public function requestGet(string $path, array $query = [], array $headers = []): array;

    public function requestPost(string $path, array $params, array $headers = []): array;
}