<?php

namespace VPMV\Marketo\API;

use VPMV\Marketo\API\Exception\MarketoException;
use VPMV\Marketo\Client\Response\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class Statuses extends ApiEndpoint
{
    public function getStatuses(string $programChannel, array $query = []): ResponseInterface
    {
        $endpoint = $this->assetURI('/channel/byName.json');
        $query['name'] = $programChannel;

        try {
            return $this->client->request('get', $endpoint, [
                'query' => $query,
            ]);
        } catch (RequestException $e) {
            throw new MarketoException('Unable to get statuses: ' . $e);
        }
    }
}
