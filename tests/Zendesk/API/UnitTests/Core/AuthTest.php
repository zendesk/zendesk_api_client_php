<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Psr7\Request;
use Zendesk\API\UnitTests\BasicTest;
use Zendesk\API\Utilities\Auth;

/**
 * Auth test class
 */
class AuthTest extends BasicTest
{
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
