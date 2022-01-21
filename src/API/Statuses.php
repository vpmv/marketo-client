<?php

namespace EventFarm\Marketo\API;

use EventFarm\Marketo\Client\MarketoClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class Statuses
{
    /** @var MarketoClientInterface */
    private $client;

    public function __construct(MarketoClientInterface $client)
    {
        $this->client = $client;
    }

    public function getStatuses(string $programChannel, array $options = []): ResponseInterface
    {
        $endpoint = '/rest/asset/v1/channel/byName.json?name=' . $programChannel;

        foreach ($options as $key => $value) {
            if (!empty($key)) {
                $endpoint = strpos($endpoint, '.json?') ? $endpoint . '&' : $endpoint . '?';
                $endpoint = $endpoint . $key . '=' . $value;
            }
        }

        try {
            return $this->client->request('get', $endpoint);
        } catch (RequestException $e) {
            throw new MarketoException('Unable to get statuses: ' . $e);
        }
    }
}
