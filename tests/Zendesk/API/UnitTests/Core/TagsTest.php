<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Tags test class
 */
class TagsTest extends BasicTest
{
    /**
     * Test that the Tags resource class actually creates the correct routes:
     *
     * tickets/{id}/tags.json
     * topics/{id}/tags.json
     * organizations/{id}/tags.json
     * users/{id}/tags.json
     */
    public function testGetRoute()
    {
        $route = $this->client->tickets(12345)->tags()->getRoute('find', ['id' => 12345]);
        $this->assertEquals('tickets/12345/tags.json', $route);
    }

    /**
     * @expectedException Zendesk\API\Exceptions\CustomException
     */
    public function testFindUnchained()
    {
        $this->client->tags()->find(1);
    }

    /**
     * @expectedException Zendesk\API\Exceptions\CustomException
     */
    public function testFindNoChainedParameter()
    {
        $this->client->tickets()->tags()->find(1);
    }
}
