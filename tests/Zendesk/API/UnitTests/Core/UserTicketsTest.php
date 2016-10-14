<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * UserTicket test class
 */
class UserTicketsTest extends BasicTest
{
    protected $number;

    /**
     * Tests if the requested endpoint can be called by the client and is passed the correct ID
     */
    public function testRelated()
    {
        $this->assertEndpointCalled(function () {
            $this->client->users(12345)->tickets()->requested();
        }, 'users/12345/tickets/requested.json');
    }

    /**
     * Tests if the requested endpoint can be called by the client and is passed the correct ID
     */
    public function testCCD()
    {
        $this->assertEndpointCalled(function () {
            $this->client->users(12345)->tickets()->ccd();
        }, 'users/12345/tickets/ccd.json');
    }

    /**
     * Tests if the requested endpoint can be called by the client and is passed the correct ID
     */
    public function testAssigned()
    {
        $this->assertEndpointCalled(function () {
            $this->client->users(12345)->tickets()->assigned();
        }, 'users/12345/tickets/assigned.json');
    }
}
