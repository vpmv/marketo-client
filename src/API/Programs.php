<?php

namespace VPMV\Marketo\API;

use VPMV\Marketo\Client\Response\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class Programs extends ApiEndpoint
{
    public function getPrograms(array $query = []): ResponseInterface
    {
        $endpoint = $this->assetURI('/programs.json');
        $query['maxReturn'] = 200;

        try {
            return $this->client->request('get', $endpoint, [
                'query' => $query,
            ]);
        } catch (RequestException $e) {
            throw new MarketoException('Unable to get programs: ' . $e);
        }
    }
}
