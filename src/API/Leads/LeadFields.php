<?php

namespace VPMV\Marketo\API\Leads;

use GuzzleHttp\Exception\RequestException;
use VPMV\Marketo\API\ApiEndpoint;
use VPMV\Marketo\API\Exception\MarketoException;
use VPMV\Marketo\Client\Response\ResponseInterface;

/**
 * @deprecated
 */
class LeadFields extends ApiEndpoint
{
    /** @deprecated  */
    public function getLeadFields(array $query = []): ResponseInterface
    {
        $endpoint = $this->restURI('/leads/describe.json');
        try {
            return $this->client->request('get', $endpoint, [
                'query' => $query,
            ]);
        } catch (RequestException $e) {
            throw new MarketoException('Unable to get lead fields: ' . $e);
        }
    }
}
