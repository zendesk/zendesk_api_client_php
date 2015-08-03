<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Macros test class
 * Class MacrosTest
 */
class MacrosTest extends BasicTest
{
    /**
     * Test the `GET /api/v2/macros/active.json` endpoint
     * Lists active macros for the current user
     */
    public function testActive()
    {
        $this->assertEndpointCalled(function () {
            $this->client->macros()->findAllActive();
        }, 'macros/active.json');
    }

    /**
     * Test the `GET /api/v2/macros/{id}/apply.json` endpoint
     * Shows the changes to the ticket
     */
    public function testApply()
    {
        $id = 1;

        $this->assertEndpointCalled(function () use ($id) {
            $this->client->macros()->apply($id);
        }, "macros/{$id}/apply.json");
    }

    /**
     * Test the `GET /api/v2/tickets/{ticket_id}/macros/{id}/apply.json` endpoint
     * Shows the ticket after the macro changes
     */
    public function testApplyToTicket()
    {
        $id       = 1;
        $ticketId = 3;

        $this->assertEndpointCalled(function () use ($id, $ticketId) {
            $this->client->macros()->applyToTicket($id, $ticketId);
        }, "tickets/{$ticketId}/macros/{$id}/apply.json");
    }
}
