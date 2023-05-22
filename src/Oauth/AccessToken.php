<?php

namespace VPMV\Marketo\Oauth;

class AccessToken implements AccessTokenInterface
{
    private $accessToken;
    private $expiresIn;
    private $lastRefresh;

    public function __construct(
        string $accessToken,
        int $expiresIn,
        int $lastRefresh = null
    ) {
        $this->accessToken = $accessToken;
        $this->expiresIn = $expiresIn;
        $this->lastRefresh = $lastRefresh ?? time();
    }

    public function getToken(): string
    {
        return $this->accessToken;
    }

    public function getExpires(): int
    {
        return $this->expiresIn;
    }

    public function getLastRefresh(): int
    {
        return $this->lastRefresh;
    }

    public function hasExpired(): bool
    {
        return time() >= $this->expiresIn - 300;
    }
}
