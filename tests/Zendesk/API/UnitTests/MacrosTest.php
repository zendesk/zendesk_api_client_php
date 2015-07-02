<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

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
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->macros()->findAllActive();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'macros/active.json',
            ]
        );
    }

    /**
     * Test the `GET /api/v2/macros/{id}/apply.json` endpoint
     * Shows the changes to the ticket
     */
    public function testApply()
    {
        $id = 1;

        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->macros()->apply($id);

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => "macros/{$id}/apply.json",
            ]
        );
    }

    /**
     * Test the `GET /api/v2/tickets/{ticket_id}/macros/{id}/apply.json` endpoint
     * Shows the ticket after the macro changes
     */
    public function testApplyToTicket()
    {
        $id = 1;
        $ticketId = 3;

        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->macros()->applyToTicket($id, $ticketId);

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => "tickets/{$ticketId}/macros/{$id}/apply.json",
                'queryParams' => []
            ]
        );
    }
}
