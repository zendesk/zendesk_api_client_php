<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Automations test class
 */
class AutomationsTest extends BasicTest
{

    /**
     * Test we can use endpoint to get active automations
     */
    public function testActive()
    {
        $this->assertEndpointCalled(function () {
            $this->client->automations()->findActive();
        }, 'automations/active.json');
    }
}
