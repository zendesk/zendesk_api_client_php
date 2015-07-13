<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

class OauthTokensTest extends BasicTest
{
    public function testRevokeEndpoint()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->oauthTokens()->revoke(1);

        $this->assertLastRequestIs(
            [
                'method'   => 'DELETE',
                'endpoint' => 'oauth/tokens/1.json'
            ]
        );
    }

    public function testCurrentEndpoint()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->oauthTokens()->current();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'oauth/tokens/current.json'
            ]
        );
    }
}
