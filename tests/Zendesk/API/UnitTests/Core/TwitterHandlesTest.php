<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

class TwitterHandlesTest extends BasicTest
{
    /**
     * Tests if the find and findAll method calls the correct endpoints
     */
    public function testRoutes()
    {
        $this->assertEndpointCalled(function () {
            $this->client->twitterHandles()->findAll();
        }, 'channels/twitter/monitored_twitter_handles.json');

        $this->assertEndpointCalled(function () {
            $this->client->twitterHandles()->find(1);
        }, 'channels/twitter/monitored_twitter_handles/1.json');
    }

    /**
     * Test that only find and findAll are present
     */
    public function testMethods()
    {
        $this->assertFalse(method_exists($this->client->twitterHandles(), 'create'));
        $this->assertFalse(method_exists($this->client->twitterHandles(), 'delete'));
        $this->assertFalse(method_exists($this->client->twitterHandles(), 'update'));
    }
}
