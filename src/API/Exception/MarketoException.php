<?php

namespace VPMV\Marketo\API\Exception;

use VPMV\Marketo\Client\Response\ResponseInterface;

/**
 * An exception for Marketo errors
 */
class MarketoException extends \Exception
{
    public static function fromResponse(string $message, ResponseInterface $response, $errorCode = 1)
    {
        return new static($message . '; Response: ' . json_encode($response->toArray()), $errorCode);
    }
}
