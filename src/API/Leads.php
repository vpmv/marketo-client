<?php

namespace EventFarm\Marketo\API;

use EventFarm\Marketo\Client\MarketoClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class Leads
{
    /** @var MarketoClientInterface */
    private $client;

    public function __construct(MarketoClientInterface $client)
    {
        $this->client = $client;
    }

    public function createOrUpdateLeads(array $objects): ResponseInterface
    {
        $endpoint = '/rest/v1/leads.json';
        $requestOptions = [
            'json'    => $objects,
            'headers' => [
                // 'Accepts' => 'application/json'
                'Content-Type' => 'application/json',
            ],
        ];


        try {
            return $this->client->request('post', $endpoint, $requestOptions);
        } catch (RequestException $e) {
            throw new MarketoException('Unable to create or update leads: ' . $e);
        }
    }

    public function updateLeadsProgramStatus(int $programId, array $options = []): ResponseInterface
    {
        $endpoint = '/rest/v1/leads/programs/' . $programId . '/status.json';

        $requestOptions = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json'    => [],
        ];

        foreach ($options as $key => $value) {
            $requestOptions['json'][$key] = $value;
        }

        try {
            return $this->client->request('post', $endpoint, $requestOptions);
        } catch (RequestException $e) {
            throw new MarketoException('Unable to update leads\' program statuses: ' . $e);
        }
    }

    public function getLeadsByProgram(int $programId, array $options = []): ResponseInterface
    {
        // Add &batchSize=1 to test batches of campaigns
        $endpoint = '/rest/v1/leads/programs/' . $programId . '.json';

        foreach ($options as $key => $value) {
            if (!empty($key)) {
                $endpoint = strpos($endpoint, '.json?') ? $endpoint . '&' : $endpoint . '?';
                $endpoint = $endpoint . $key . '=' . $value;
            }
        }

        try {
            return $this->client->request('get', $endpoint);
        } catch (RequestException $e) {
            throw new MarketoException('Unable to get leads by program: ' . $e);
        }
    }
}
