<?php

namespace VPMV\Marketo\API\Assets;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use VPMV\Marketo\API\ApiEndpoint;
use VPMV\Marketo\API\Exception\MarketoException;
use VPMV\Marketo\Client\Response\AssetResponse;

class Folders extends ApiEndpoint
{
    /**
     * Query all folders
     *
     * @param array $query
     *
     * @return \VPMV\Marketo\Client\Response\ResponseInterface Folder listing
     */
    public function getFolders(array $query = [])
    {
        $endpoint = $this->assetURI('/folders.json');

        return $this->client->request('get', $endpoint, ['query' => $query]);
    }

    /**
     * Create a new folder
     *
     * @param string      $name        Folder name
     * @param int         $parent      Parent folder ID
     * @param string|null $description Folder description
     *
     * @return int Folder ID
     * @throws \VPMV\Marketo\API\Exception\MarketoException
     */
    public function createFolder(string $name, int $parent, ?string $description = null): int
    {
        $endpoint = $this->assetURI('/folders.json');
        $body = [
            'name'        => $name,
            'description' => $description,
            'parent'      => json_encode([
                'id'   => $parent,
                'type' => 'Folder',
            ]),
        ];
        try {
            /** @var AssetResponse $res */
            $res = $this->client->request('post', $endpoint, [
                'form_params' => $body,
            ], AssetResponse::class);
            if (!$res->isSuccessful()) {
                throw MarketoException::fromResponse("Could not create Folder $name", $res);
            }
            return $res->getResult()['folderId']['id'];
        } catch (BadResponseException $e) {
            throw new MarketoException('Unable to create Folder', 0, $e);
        }
    }

    /**
     * Query folder ID by name
     *
     * @param string          $name Folder name
     * @param int|string|null $root Parent folder name or ID
     *
     * @return int|null Folder ID
     * @throws \VPMV\Marketo\API\Exception\MarketoException
     */
    public function getFolderByName(string $name, $root = null): ?int
    {
        $endpoint = $this->assetURI('/folder/byName.json');
        $query = [
            'name' => $name,
            'type' => 'Folder',
        ];
        if ($root) {
            if (!is_int($root) && !ctype_digit($root)) {
                $root = $this->getFolderByName($root);
            }
            $query['root'] = json_encode([
                'id'   => $root,
                'type' => 'Folder',
            ]);
        }
        try {
            /** @var AssetResponse $res */
            $res = $this->client->request('get', $endpoint, [
                'query' => $query,
            ], AssetResponse::class);

            if (!$res->isSuccessful()) {
                throw MarketoException::fromResponse('Could not retrieve Folder '. $name, $res, 1);
            }
            return $res->getResult()[0]['folderId']['id'];
        } catch (RequestException $e) {
            throw new MarketoException('Could not query folder', 1, $e);
        }
    }
}
