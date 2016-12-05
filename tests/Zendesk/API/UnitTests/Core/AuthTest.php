<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Zendesk\API\Http;
use Zendesk\API\HttpClient;
use Zendesk\API\UnitTests\BasicTest;
use Zendesk\API\Utilities\Auth;

/**
 * Auth test class
 */
class AuthTest extends BasicTest
{
    /**
     * Test if request is still sent even without authentication
     */
    public function testAnonymousAccess()
    {
        // mock client
        $client = $this
            ->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $client->method('getHeaders')->willReturn([]);
        $client->expects(self::once())->method('getAuth')->willReturn(null);

        // prepareRequest should not be called
        $auth = $this
            ->getMockBuilder(Auth::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::never())->method('prepareRequest');
        $client->expects(self::once())->method('getAuth')->willReturn($auth);

        // send request
        $client->guzzle = $this->getMockBuilder(Client::class)->getMock();
        $client->guzzle->method('send')->willReturn(new Response);
        Http::send($client, '');
    }

    /**
     * Test the preparing of a request for basic authentication.
     */
    public function testPrepareBasicAuth()
    {
        $this->client->setAuth('basic', ['username' => $this->username, 'token' => $this->token]);

        $currentRequest        = new Request('GET', 'http://www.endpoint.com/test.json');
        $currentRequestOptions = ['existing' => 'option'];

        list ($request, $requestOptions) = $this->client->getAuth()->prepareRequest(
            $currentRequest,
            $currentRequestOptions
        );

        $this->assertInstanceOf(Request::class, $request, 'Should have returned a request');

        $this->assertArrayHasKey('existing', $requestOptions);
        $this->assertArrayHasKey('auth', $requestOptions);
        $this->assertEquals($this->username . '/token', $requestOptions['auth'][0]);
        $this->assertEquals($this->token, $requestOptions['auth'][1]);
        $this->assertEquals(Auth::BASIC, $requestOptions['auth'][2]);
    }

    /**
     * Test the preparing of a request for oauth authentication.
     */
    public function testPrepareOAuth()
    {
        $this->client->setAuth('oauth', ['token' => $this->oAuthToken]);

        $currentRequest        = new Request('GET', 'http://www.endpoint.com/test.json');
        $currentRequestOptions = ['existing' => 'option'];

        list ($request, $requestOptions) = $this->client->getAuth()->prepareRequest(
            $currentRequest,
            $currentRequestOptions
        );

        $this->assertEquals($currentRequestOptions, $requestOptions);
        $this->assertNotEmpty($authHeader = $request->getHeader('Authorization'));
        $this->assertEquals('Bearer ' . $this->oAuthToken, $authHeader[0]);
    }
}
