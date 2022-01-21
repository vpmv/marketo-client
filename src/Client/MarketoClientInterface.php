<?php

namespace EventFarm\Marketo\Client;

use Psr\Http\Message\ResponseInterface;

interface MarketoClientInterface
{
    public function request(string $method, string $uri, array $options = []): ResponseInterface;
}
