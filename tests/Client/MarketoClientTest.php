<?php

namespace EventFarm\Marketo\Tests\Client;

use EventFarm\Marketo\Client\MarketoClient;
use EventFarm\Marketo\Client\TokenRefreshInterface;
use EventFarm\Marketo\Oauth\AccessToken;
use EventFarm\Marketo\Oauth\MarketoProvider;
use EventFarm\Marketo\Oauth\RetryAuthorizationTokenFailedException;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class MarketoClientTest extends TestCase
{
    public function testExceptionIsThrownWhenClientRetriesMoreThanMaxRetry()
    {
        $restClient = \Mockery::mock(ClientInterface::class);
        $provider = \Mockery::mock(MarketoProvider::class);
        $accessToken = \Mockery::mock(AccessToken::class);
        $accessToken->shouldReceive('getLastRefresh')
            ->andReturn(1234567890);
        $accessToken->shouldReceive('getExpires')
            ->andReturn(1000);
        $tokenRefreshCallback = \Mockery::mock(TokenRefreshInterface::class);
        $tokenRefreshCallback->shouldReceive('tokenRefreshCallback');
        $provider->shouldReceive('getAccessToken')
            ->andReturn($accessToken);
        $accessToken->shouldReceive('getToken')
            ->andReturn('MOCKACCESSTOKEN');
        $failedResponse = \Mockery::mock(ResponseInterface::class);
        $failedResponse->shouldReceive('getStatusCode')
            ->andReturn(401);
        $failedResponse->shouldReceive('getBody')
            ->andReturn($failedResponse);
        $failedResponse->shouldReceive('__toString')
            ->andReturn('{"result":[{}]}');
        $restClient->shouldReceive('request')
            ->andReturn($failedResponse)
            ->times(3);

        $maxRetry = 3;
        $marketoClient = MarketoClient::with(
            $restClient,
            $provider,
            $accessToken,
            $tokenRefreshCallback,
            $maxRetry
        );
        try {
            $marketoClient->request('GET', '/example/getExample');
            $this->fail('An exception should have been thrown');
        } catch (RetryAuthorizationTokenFailedException $e) {
        }
        $this->assertEquals(1, 1);
        \Mockery::close();
    }

    public function testFailWith401ThenRetryAndSucceedBeforeMaxRetryLimit()
    {
        $restClient = \Mockery::mock(ClientInterface::class);
        $provider = \Mockery::mock(MarketoProvider::class);
        $accessToken = \Mockery::mock(AccessToken::class);
        $accessToken->shouldReceive('getLastRefresh')
            ->andReturn(1234567890);
        $accessToken->shouldReceive('getExpires')
            ->andReturn(1000);
        $tokenRefreshCallback = \Mockery::mock(TokenRefreshInterface::class);
        $tokenRefreshCallback->shouldReceive('tokenRefreshCallback');
        $provider->shouldReceive('getAccessToken')
            ->andReturn($accessToken);
        $accessToken->shouldReceive('getToken')
            ->andReturn('MOCKACCESSTOKEN');
        $failedResponse = \Mockery::mock(ResponseInterface::class);
        $failedResponse->shouldReceive('getStatusCode')
            ->andReturn(401);
        $failedResponse->shouldReceive('getBody')
            ->andReturn($failedResponse);
        $failedResponse->shouldReceive('__toString')
            ->andReturn('{"result":[{}]}');
        $successResponse = \Mockery::mock(ResponseInterface::class);
        $successResponse->shouldReceive('getStatusCode')
            ->andReturn(200);
        $successResponse->shouldReceive('getBody')
            ->andReturn($successResponse);
        $successResponse->shouldReceive('__toString')
            ->andReturn('{"result":[{}]}');
        $restClient->shouldReceive('request')
            ->andReturn($failedResponse)
            ->times(2);
        $restClient->shouldReceive('request')
            ->andReturn($successResponse)
            ->once();
        $maxRetry = 3;
        $marketoClient = MarketoClient::with(
            $restClient,
            $provider,
            $accessToken,
            $tokenRefreshCallback,
            $maxRetry
        );
        $response = $marketoClient->request('GET', '/example/getExample');
        $this->assertSame(200, $response->getStatusCode());
        \Mockery::close();
    }

    public function testFailWithMarketoErrorThenRetryAndSucceedBeforeMaxRetryLimit()
    {
        $restClient = \Mockery::mock(ClientInterface::class);
        $provider = \Mockery::mock(MarketoProvider::class);
        $accessToken = \Mockery::mock(AccessToken::class);
        $accessToken->shouldReceive('getLastRefresh')
            ->andReturn(1234567890);
        $accessToken->shouldReceive('getExpires')
            ->andReturn(1000);
        $tokenRefreshCallback = \Mockery::mock(TokenRefreshInterface::class);
        $tokenRefreshCallback->shouldReceive('tokenRefreshCallback');
        $provider->shouldReceive('getAccessToken')
            ->andReturn($accessToken);
        $accessToken->shouldReceive('getToken')
            ->andReturn('MOCKACCESSTOKEN');
        $failedResponse = \Mockery::mock(ResponseInterface::class);
        $failedResponse->shouldReceive('getStatusCode')
            ->andReturn(200);
        $failedResponse->shouldReceive('getBody')
            ->andReturn($failedResponse);
        $failedResponse->shouldReceive('__toString')
            ->andReturn('{"errors":[{"code": 601, "message": "Access token invalid"}]}');
        $successResponse = \Mockery::mock(ResponseInterface::class);
        $successResponse->shouldReceive('getStatusCode')
            ->andReturn(200);
        $successResponse->shouldReceive('getBody')
            ->andReturn($successResponse);
        $successResponse->shouldReceive('__toString')
            ->andReturn('{"result":[{}]}');
        $restClient->shouldReceive('request')
            ->andReturn($failedResponse)
            ->times(2);
        $restClient->shouldReceive('request')
            ->andReturn($successResponse)
            ->once();
        $maxRetry = 3;
        $marketoClient = MarketoClient::with(
            $restClient,
            $provider,
            $accessToken,
            $tokenRefreshCallback,
            $maxRetry
        );
        $response = $marketoClient->request('GET', '/example/getExample');
        $this->assertSame(200, $response->getStatusCode());
        \Mockery::close();
    }
}
