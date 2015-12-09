<?php

namespace Zendesk\API\UnitTests\Core;

use Faker\Factory;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Triggers test class
 */
class TriggersTest extends BasicTest
{
    /**
     * Tests if the client can call and build the active triggers endpoint
     */
    public function testActive()
    {
        $this->assertEndpointCalled(function () {
            $this->client->triggers()->findActive();
        }, 'triggers/active.json');
    }

    /**
     * Tests if the client can call and build the active triggers endpoint
     */
    public function testFind()
    {
        $faker = Factory::create();
        $triggerName = $faker->word;

        $this->assertEndpointCalled(function () use ($triggerName) {
            $this->client->triggers()->find($triggerName);
        }, "triggers/{$triggerName}");
    }
}
