<?php

namespace AuthManager;

interface OpenIDInterface
{
    public function getAuthURI(): string;
}