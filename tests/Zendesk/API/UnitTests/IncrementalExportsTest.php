<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

/**
 * Class IncrementalExportsTest
 */
class IncrementalExportsTest extends BasicTest
{
    /**
     * Test get incremental export for tickets
     */
    public function testTickets()
    {
        $this->getEndpointTest('tickets', 'incremental/tickets.json');
    }

    /**
     * Test get incremental export for ticket events
     *
     */
    public function testTicketEvents()
    {
        $this->getEndpointTest('ticketEvents', 'incremental/ticket_events.json');
    }

    /**
     * Test get incremental export for organizations
     */
    public function testOrganizations()
    {
        $this->getEndpointTest('organizations', 'incremental/organizations.json');
    }

    /**
     * Test get incremental export for users
     */
    public function testUsers()
    {
        $this->getEndpointTest('users', 'incremental/users.json');
    }

    /**
     * Test for the get endpoint using the given method and endpoint
     *
     * @param $method
     * @param $endpoint
     */
    private function getEndpointTest($method, $endpoint)
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $queryParams = [
            'start_time' => 1332034771,
        ];

        $this->client->incrementalExports()->$method($queryParams);

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => $endpoint,
                'queryParams' => $queryParams,
            ]
        );
    }
}
