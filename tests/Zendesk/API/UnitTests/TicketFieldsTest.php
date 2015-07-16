<?php

namespace Zendesk\API\UnitTests;

use Zendesk\API\Resources\TicketFields;

/**
 * Ticket Fields test class
 */
class TicketFieldsTest extends BasicTest
{

    /**
     * Test that the resource name was set correctly
     */
    public function testResourceNameWasSetCorrectly()
    {
        $ticketFields = new TicketFields($this->client);

        $this->assertEquals('ticket_fields', $ticketFields->getResourceName());
    }
}
