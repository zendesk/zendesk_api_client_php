<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * User Tickets test class
 */
class UserTicketsTest extends BasicTest
{
    /**
     * Test findAll method
     */
    public function testAll()
    {

        $userId = 1234;
        $this->assertEndpointCalled(function () use ($userId) {
            $this->client->users($userId)->tickets()->findAll();
        }, "users/{$userId}/tickets.json");

    }
}
