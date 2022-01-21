<?php

namespace EventFarm\Marketo\API;

use EventFarm\Marketo\Client\MarketoClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class Campaigns
{
    /** @var MarketoClientInterface */
    private $client;

    public function __construct(MarketoClientInterface $client)
    {
        $this->client = $client;
    }

    public function getCampaigns(array $options = []): ResponseInterface
    {
        $endpoint = '/rest/v1/campaigns.json';

        foreach ($options as $key => $value) {
            if (!empty($key)) {
                $endpoint = strpos($endpoint, '.json?') ? $endpoint . '&' : $endpoint . '?';
                $endpoint = $endpoint . $key . '=' . $value;
            }
        }

        try {
            return $this->client->request('get', $endpoint);
        } catch (RequestException $e) {
            throw new MarketoException('Unable to get campaigns: ' . $e);
        }
    }

    public function triggerCampaign(int $campaignId, array $options): ResponseInterface
    {
        $endpoint = '/rest/v1/campaigns/' . $campaignId . '/trigger.json';
        $requestOptions = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json'    => $options,
        ];

        try {
            return $this->client->request('post', $endpoint, $requestOptions);
        } catch (RequestException $e) {
            throw new MarketoException('Unable to trigger campaign: ' . $e);
        }
    }
}
