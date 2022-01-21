<?php

namespace EventFarm\Marketo\API;

use EventFarm\Marketo\Client\MarketoClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class Programs
{
    /** @var MarketoClientInterface */
    private $marketoRestClient;

    public function __construct(MarketoClientInterface $client)
    {
        $this->marketoRestClient = $client;
    }

    public function getPrograms(array $options = []): ResponseInterface
    {
        $endpoint = '/rest/asset/v1/programs.json?maxReturn=200';

        foreach ($options as $key => $value) {
            if (!empty($key)) {
                $endpoint = strpos($endpoint, '.json?') ? $endpoint . '&' : $endpoint . '?';
                $endpoint = $endpoint . $key . '=' . $value;
            }
        }

        try {
            return $this->marketoRestClient->request('get', $endpoint);
        } catch (RequestException $e) {
            throw new MarketoException('Unable to get programs: ' . $e);
        }
    }
}
