<?php

namespace Netitus\Marketo;

use Netitus\Marketo\API;
use Netitus\Marketo\Client\MarketoClient;
use Netitus\Marketo\Client\MarketoClientInterface;

class Marketo
{
    /** @var \Netitus\Marketo\Client\MarketoClientInterface */
    private $client;

    public function __construct(MarketoClientInterface $client)
    {
        $this->client = $client;
    }

    public static function withDefaultClient(string $clientId, string $clientSecret, string $baseUrl): Marketo
    {
        $client = MarketoClient::withDefaults($clientId, $clientSecret, $baseUrl);
        return new static($client);
    }

    /**
     * @return \Netitus\Marketo\Client\MarketoClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    public function stats()
    {
        return new API\Stats($this->client);
    }

    public function programs()
    {
        return new API\Programs($this->client);
    }

    public function campaigns()
    {
        return new API\Campaigns($this->client);
    }

    public function statuses()
    {
        return new API\Statuses($this->client);
    }

    /**
     * @return \Netitus\Marketo\API\Leads\LeadFields
     * @deprecated
     */
    public function leadFields()
    {
        return new API\Leads\LeadFields($this->client);
    }

    public function leads()
    {
        return new API\Leads\Leads($this->client);
    }

    public function companies()
    {
        return new API\Leads\Companies($this->client);
    }

    public function salesPersons()
    {
        return new API\Leads\SalesPersons($this->client);
    }

    public function partitions()
    {
        return new API\Partitions($this->client);
    }

    public function folders()
    {
        return new API\Assets\Folders($this->client);
    }

    public function snippets()
    {
        return new API\Assets\Snippets($this->client);
    }
}
