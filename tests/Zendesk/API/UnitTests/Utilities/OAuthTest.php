<?php

namespace Zendesk\API\UnitTests\Utilities;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\UnitTests\BasicTest;
use Zendesk\API\Utilities\OAuth;

class OAuthTest extends BasicTest
{
    /**
     * Tests if the OAuth::getAuthUrl function returns a correct URL.
     */
    public function testAuthUrl()
    {
        $params = [
            'client_id'    => 'test_client',
            'state'        => 'St8fulbar',
        ];

        $expected = 'https://z3ntestsub.zendesk.com/oauth/authorizations/new?response_type=code&client_id=test_client&state=St8fulbar&scope=read+write';

        $this->assertEquals($expected, OAuth::getAuthUrl('z3ntestsub', $params));
    }

    public function testAccessTokenIsRequested()
    {
        $this->mockApiResponses([
            new Response(200, [], json_encode(['access_token' => 12345]))
        ]);

        $params = [
            'code'          => 'adwo123ijo',
            'client_id'     => 'test_client',
            'client_secret' => 'dwapjoJ123d8w9a01-',
            'grant_type'    => 'authorization_code',
            'scope'         => 'read write',
            'redirect_uri'  => 'https://test.foo.com',
        ];

        OAuth::getAccessToken($this->client->guzzle, 'test', $params);

        $this->assertLastRequestIs([
            'method' => 'POST',
            'requestUri' => 'https://test.zendesk.com/oauth/tokens',
            'postFields' => $params,
            'headers' => [
                'accept' => false,
            ],
            'apiBasePath' => '/',
        ]);
    }

    /**
     * Tests if the OAuth::getAuthUrl function returns a correct URL.
     */
    public function testConfigurableDomain()
    {
        $params = [
            'client_id'    => 'test_client',
            'state'        => 'St8fulbar',
        ];

        $expected = 'https://z3ntestsub.testDomain.com/oauth/authorizations/new?response_type=code&client_id=test_client&state=St8fulbar&scope=read+write';

        $this->assertEquals($expected, OAuth::getAuthUrl('z3ntestsub', $params, 'testDomain.com'));
    }
}
