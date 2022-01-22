<?php

namespace EventFarm\Marketo\Client;

use EventFarm\Marketo\Client\Response\ResponseInterface;

interface MarketoClientInterface
{
    public function request(string $method, string $uri, array $options = []): ResponseInterface;

    /**
     * Return API endpoint version
     *
     * @return int
     */
    public function version(): int;
}
