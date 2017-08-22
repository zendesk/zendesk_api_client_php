<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Organization Tickets test class
 */
class OrganizationTicketsTest extends BasicTest
{
    /**
     * Test findAll method
     */
    public function testAll()
    {

        $organizationId = 1234;
        $this->assertEndpointCalled(function () use ($organizationId) {
            $this->client->organizations($organizationId)->tickets()->findAll();
        }, "organizations/{$organizationId}/tickets.json");
    }
}
