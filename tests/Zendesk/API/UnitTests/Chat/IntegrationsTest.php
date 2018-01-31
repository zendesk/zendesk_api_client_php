<?php

namespace Zendesk\API\UnitTests\Chat;

use Zendesk\API\UnitTests\BasicTest;

class IntegrationsTest extends BasicTest
{
    /**
     * Tests that the Chat integration can be found for an account
     */
    public function testFind()
    {
        $this->assertEndpointCalled(function () {
             $this->client->chat->integrations()->find();
        }, 'zopim_integration.json', 'GET');
    }
}
