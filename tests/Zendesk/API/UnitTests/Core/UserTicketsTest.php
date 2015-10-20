<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * User Tickets test class
 */
class UserTicketsTest extends BasicTest
{
    /**
     * Test requested method
     */
    public function testRequested()
    {

        $userId = 1234;
        $this->assertEndpointCalled(function () use ($userId) {
            $this->client->users($userId)->tickets()->requested();
        }, "users/{$userId}/tickets/requested.json");

    }

    /**
     * Test assigned method
     */
    public function testAssigned()
    {

        $userId = 1234;
        $this->assertEndpointCalled(function () use ($userId) {
            $this->client->users($userId)->tickets()->assigned();
        }, "users/{$userId}/tickets/assigned.json");

    }

    /**
     * Test requested method
     */
    public function testCcd()
    {

        $userId = 1234;
        $this->assertEndpointCalled(function () use ($userId) {
            $this->client->users($userId)->tickets()->ccd();
        }, "users/{$userId}/tickets/ccd.json");

    }
}
