<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

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
        $queryParams = [
            'start_time' => 1332034771,
        ];

        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->incremental()->tickets($queryParams);
        }, 'incremental/tickets.json', 'GET', ['queryParams' => $queryParams]);
    }

    /**
     * Test get incremental export for ticket events
     *
     */
    public function testTicketEvents()
    {
        $queryParams = [
            'start_time' => 1332034771,
        ];

        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->incremental()->ticketEvents($queryParams);
        }, 'incremental/ticket_events.json', 'GET', ['queryParams' => $queryParams]);
    }

    /**
     * Test get incremental export for organizations
     */
    public function testOrganizations()
    {
        $queryParams = [
            'start_time' => 1332034771,
        ];

        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->incremental()->organizations($queryParams);
        }, 'incremental/organizations.json', 'GET', ['queryParams' => $queryParams]);
    }

    /**
     * Test get incremental export for users
     */
    public function testUsers()
    {
        $queryParams = [
            'start_time' => 1332034771,
        ];

        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->incremental()->users($queryParams);
        }, 'incremental/users.json', 'GET', ['queryParams' => $queryParams]);
    }
}
