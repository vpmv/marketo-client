<?php

namespace VPMV\Marketo;

use VPMV\Marketo\API;
use VPMV\Marketo\Client\MarketoClient;
use VPMV\Marketo\Client\MarketoClientInterface;

class Marketo
{
    private MarketoClientInterface $client;

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
     * @return \VPMV\Marketo\Client\MarketoClientInterface
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
     * @return \VPMV\Marketo\API\Leads\LeadFields
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
