<?php

namespace VPMV\Marketo\Oauth;

interface MarketoProviderInterface
{
    /**
     * Requests an access token using a specified grant and option set.
     *
     * @param mixed $grant
     * @param array $options
     *
     * @return AccessTokenInterface
     */
    public function refreshAccessToken(array $options = []): AccessTokenInterface;
}
