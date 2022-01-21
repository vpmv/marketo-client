<?php

namespace EventFarm\Marketo\API;

use Psr\Http\Message\ResponseInterface;

class ResponseHelper
{
    public static function asJson(ResponseInterface $response)
    {
        return json_decode($response->getBody()->__toString());
    }

}