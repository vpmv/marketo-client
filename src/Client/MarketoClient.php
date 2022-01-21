<?php

namespace EventFarm\Marketo\Client;

use EventFarm\Marketo\Oauth\AccessToken;
use EventFarm\Marketo\Oauth\MarketoProvider;
use EventFarm\Marketo\Oauth\MarketoProviderInterface;
use EventFarm\Marketo\Oauth\RetryAuthorizationTokenFailedException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class MarketoClient implements MarketoClientInterface
{
    protected const DEFAULT_MAX_RETRY_REQUESTS   = 2;
    protected const DEFAULT_TOKEN_REFRESH_OBJECT = null;
    protected const TOKEN_INVALID                = 601;
    protected const TOKEN_EXPIRED                = 602;

    private ClientInterface          $client;
    private MarketoProviderInterface $provider;
    private ?AccessToken             $accessToken;
    private ?TokenRefreshInterface   $tokenRefreshCallback;
    private int                      $maxRetryRequests;


    private function __construct(
        ClientInterface $guzzleClient,
        MarketoProviderInterface $marketoProvider,
        ?TokenRefreshInterface $tokenRefreshObject = null,
        ?AccessToken $accessToken = null,
        int $maxRetryRequests
    ) {
        $this->client = $guzzleClient;
        $this->provider = $marketoProvider;
        $this->accessToken = $accessToken;
        $this->tokenRefreshCallback = $tokenRefreshObject;
        $this->maxRetryRequests = $maxRetryRequests;
    }

    public static function with(
        ClientInterface $guzzleClient,
        MarketoProviderInterface $marketoProvider,
        ?AccessToken $accessToken = null,
        TokenRefreshInterface $tokenRefreshObject = self::DEFAULT_TOKEN_REFRESH_OBJECT,
        int $maxRetryRequests = null
    ) {
        if (null === $maxRetryRequests) {
            $maxRetryRequests = static::DEFAULT_MAX_RETRY_REQUESTS;
        }
        return new static($guzzleClient, $marketoProvider, $tokenRefreshObject, $accessToken, $maxRetryRequests);
    }

    public static function withDefaults(
        string $clientId,
        string $clientSecret,
        string $baseUrl,
        TokenRefreshInterface $tokenRefreshObject = null,
        int $maxRetryRequests = null
    ) {
        if (null === $maxRetryRequests) {
            $maxRetryRequests = static::DEFAULT_MAX_RETRY_REQUESTS;
        }

        $guzzleClient = new Client([
            'http_errors' => false,
            'base_uri'    => $baseUrl,
        ]);
        $marketoProvider = MarketoProvider::createDefaultProvider(
            $clientId,
            $clientSecret,
            $baseUrl,
        );

        return new static($guzzleClient, $marketoProvider, $tokenRefreshObject, null, $maxRetryRequests);
    }


    private function getAccessToken(): string
    {
        return $this->accessToken->getToken();
    }

    private function isTokenValid(\stdClass $responseBody): bool
    {
        /* Depending on the endpoint, the JSON Marketo returns will always contain an errors key (like getPrograms
        does) or will only contain an errors key if there are errors (like getCampaigns does) */
        if (property_exists($responseBody, "errors") && !empty($responseBody->errors)) {
            $errorCodes = [self::TOKEN_INVALID, self::TOKEN_EXPIRED];
            foreach ($responseBody->errors as $error) {
                if (in_array($error->code, $errorCodes)) {
                    return false;
                }
            }
        }
        return true;
    }

    private function isResponseAuthorized(ResponseInterface $response): bool
    {
        return $response->getStatusCode() !== 401;
    }

    private function refreshAccessToken()
    {
        $tokenResponse = $this->provider->getAccessToken('client_credentials');
        if (!empty($this->tokenRefreshCallback)) {
            $this->tokenRefreshCallback->tokenRefreshCallback($tokenResponse);
        }

        $this->accessToken = $tokenResponse;
    }

    private function retryRequest(string $method, string $uri, array $options): ResponseInterface
    {
        $attempts = 0;
        do {
            $expirationTime = $this->accessToken->getLastRefresh() + $this->accessToken->getExpires();

            if (time() >= $expirationTime - 300) {
                $this->refreshAccessToken();
            }

            $options['headers']['Authorization'] = 'Bearer ' . $this->getAccessToken();
            $response = $this->client->request($method, $uri, $options);
            $responseBody = json_decode($response->getBody()->__toString());

            $isAuthorized = $this->isResponseAuthorized($response);
            $isTokenValid = $this->isTokenValid($responseBody);

            if (!$isAuthorized || !$isTokenValid) {
                $this->refreshAccessToken();
            }
            $attempts++;
        } while ((!$isAuthorized || !$isTokenValid) && $attempts < $this->maxRetryRequests);

        if (!$isAuthorized || !$isTokenValid) {
            throw new RetryAuthorizationTokenFailedException(
                'Max retry limit of ' . $this->maxRetryRequests . 'has been reached. Retrieving access token failed.'
            );
        }

        return $response;
    }

    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        return $this->retryRequest($method, $uri, $options);
    }
}
