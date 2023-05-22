<?php

namespace VPMV\Marketo\Oauth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken as LeagueAccessToken;
use Psr\Http\Message\ResponseInterface;

class MarketoProvider extends AbstractProvider implements MarketoProviderInterface
{
    public string $baseUrl; // gets assigned in AbstractProvider constructor

    public function __construct(string $clientId, string $clientSecret, string $baseUrl)
    {
        parent::__construct([
            'clientId'     => $clientId,
            'clientSecret' => $clientSecret,
            'baseUrl'      => $baseUrl,
        ]);
    }

    public function refreshAccessToken(array $options = []): AccessTokenInterface
    {
        $leagueAT = $this->getAccessToken('client_credentials', $options);
        return new AccessToken(
            $leagueAT->getToken(),
            $leagueAT->getExpires()
        );
    }

    /**
     * Returns the base URL for requesting an access token
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->baseUrl . "/identity/oauth/token";
    }

    /**
     * Check a provider response for errors.
     *
     * @param ResponseInterface $response
     * @param array|string      $data
     *
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw new IdentityProviderException(
                $data['error'] ?: $response->getReasonPhrase(),
                $response->getStatusCode(),
                $response->getBody()
            );
        }
    }

    public function getBaseAuthorizationUrl()
    {
    }
    public function getResourceOwnerDetailsUrl(LeagueAccessToken $token)
    {
    }
    protected function getDefaultScopes()
    {
    }
    protected function createResourceOwner(array $response, LeagueAccessToken $token)
    {
    }
}
