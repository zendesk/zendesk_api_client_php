<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Class OAuthTokensTest
 */
class OAuthTokensTest extends BasicTest
{
    /**
     * Test for revoke method
     */
    public function testRevokeEndpoint()
    {
        $resourceId = 183;
        $this->assertEndpointCalled(function () use ($resourceId) {
            $this->client->oauthTokens()->revoke($resourceId);
        }, "oauth/tokens/{$resourceId}.json", 'DELETE');
    }

    /**
     * Test for current method
     */
    public function testCurrentEndpoint()
    {
        $this->assertEndpointCalled(function () {
            $this->client->oauthTokens()->current();
        }, 'oauth/tokens/current.json');
    }
}
