<?php

namespace Netitus\Marketo\API;

use Netitus\Marketo\Client\Response\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

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
