<?php

namespace Zendesk\API\UnitTests;

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
        $this->endpointTest('GET', 'tickets', 'incremental/tickets.json');
    }

    /**
     * Test get incremental export for ticket events
     *
     */
    public function testTicketEvents()
    {
        $this->endpointTest('GET', 'ticketEvents', 'incremental/ticket_events.json');
    }

    /**
     * Test get incremental export for organizations
     */
    public function testOrganizations()
    {
        $this->endpointTest('GET', 'organizations', 'incremental/organizations.json');
    }

    /**
     * Test get incremental export for users
     */
    public function testUsers()
    {
        $this->endpointTest('GET', 'users', 'incremental/users.json');
    }
}
