<?php

namespace EventFarm\Marketo\Client;

use EventFarm\Marketo\Oauth\AccessToken;

interface TokenRefreshInterface
{
    public function tokenRefreshCallback(AccessToken $accessToken);
}
