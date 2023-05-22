<?php

namespace VPMV\Marketo\API;

use VPMV\Marketo\API\Exception;
use VPMV\Marketo\Client\MarketoClientInterface;
use VPMV\Marketo\Client\Response\ResponseInterface;

class ApiEndpoint
{
    protected MarketoClientInterface $client;
    protected ?string                $version;

    public function __construct(MarketoClientInterface $client)
    {
        $this->client = $client;
        $this->version = 'v' . $client->version();
    }

    protected function restURI(string $endpoint): string
    {
        return '/rest/' . $this->version . $endpoint;
    }

    protected function assetURI(string $endpoint): string
    {
        return '/rest/asset/' . $this->version . $endpoint;
    }

    protected function bulkURI(string $endpoint): string
    {
        return '/bulk/' . $this->version . $endpoint;
    }

    /**
     * Evaluates response/error code and throws appropriate exception
     *
     * @param \VPMV\Marketo\Client\Response\ResponseInterface $response
     *
     * @return void
     * @throws \VPMV\Marketo\API\Exception\MarketoException
     */
    protected function evaluateResponse(ResponseInterface $response): void
    {
        $code = $response->getStatusCode();
        $message = '';
        if ($code == 200) {
            if (!$response->hasErrors()) {
                return;
            } else {
                $err = $response->get('errors')[0];
                $code = (int)$err['code'];
                $message = $err['message'];
            }
        }
        switch ($code) {
            case 601:
                throw new Exception\UnauthorizedException('Unauthorized', $code);
            case 602:
                throw new Exception\UnauthorizedException('Access token expired', $code);
            case 603:
                throw new Exception\UnauthorizedException('Permission denied', $code);
            case 606:
                throw new Exception\RateLimitException('Exceeded rate limit; ' . $message, $code);
            case 607:
                throw new Exception\RateLimitException('Exceeded daily quota; ' . $message, $code);
            case 608:
                throw new Exception\ServiceUnavailableException('Service temporarily unavailable; ' . $message, $code);
            default:
                throw new Exception\RequestException('Request exception: ' . $message, $code);
        }
    }
}
