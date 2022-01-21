<?php

namespace EventFarm\Marketo\API;

use EventFarm\Marketo\Client\MarketoClientInterface;
use EventFarm\Marketo\RestClient\MarketoRestClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class Partitions
{
    /** @var MarketoClientInterface */
    private $client;

    public function __construct(MarketoClientInterface $client)
    {
        $this->client = $client;
    }

    public function getPartitions(array $options = []): ResponseInterface
    {
        $endpoint = '/rest/v1/leads/partitions.json';

        foreach ($options as $key => $value) {
            if (!empty($key)) {
                $endpoint = strpos($endpoint, '.json?') ? $endpoint . '&' : $endpoint . '?';
                $endpoint = $endpoint . $key . '=' . $value;
            }
        }

        try {
            return $this->client->request('get', $endpoint);
        } catch (RequestException $e) {
            throw new MarketoException('Unable to get partitions: ' . $e);
        }
    }
}
