<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * SuspendedTickets test class
 */
class SuspendedTicketsTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    /*
     * Need at least one suspend ticket for this test.
     */
    public function testAll()
    {
        $tickets = $this->client->suspendedTickets()->findAll();
        $this->assertEquals(is_object($tickets), true, 'Should return an object');
        $this->assertEquals(is_array($tickets->suspended_tickets), true,
            'Should return an object containing an array called "suspended_tickets"');
        $this->assertGreaterThan(0, $tickets->suspended_tickets[0]->id,
            'Returns a non-numeric id for suspended_tickets[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $id = $this->client->suspendedTickets()->findAll()->suspended_tickets[0]->id;
        $ticket = $this->client->suspendedTicket($id)->find();
        $this->assertEquals(is_object($ticket), true, 'Should return an object');
        $this->assertEquals(is_object($ticket->suspended_ticket), true,
            'Should return an object called "suspended_ticket"');
        $this->assertGreaterThan(0, $ticket->suspended_ticket->id, 'Returns a non-numeric id for view');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testRecover()
    {
        $this->markTestSkipped(
            'The only way to recover a ticket is to suspend it first (but that would result in a suspended user too)'
        );
        $ticket = $this->client->suspendedTicket(256155729)->recover();
        $this->assertEquals(is_object($ticket), true, 'Should return an object');
        $this->assertEquals(is_object($ticket->ticket), true, 'Should return an object called "ticket"');
        $this->assertGreaterThan(0, $ticket->ticket->id, 'Returns a non-numeric id for ticket');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testDelete()
    {
        $this->markTestSkipped(
            'The only way to delete a suspended ticket is to suspend it first (but that would result in a suspended user too)'
        );
        $ticket = $this->client->suspendedTicket(256155729)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}
