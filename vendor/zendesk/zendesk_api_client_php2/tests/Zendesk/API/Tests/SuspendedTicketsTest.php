<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * SuspendedTickets test class
 */
class SuspendedTicketsTest extends \PHPUnit_Framework_TestCase {

    private $client;
    private $subdomain;
    private $username;
    private $password;
    private $token;
    private $oAuthToken;

    public function __construct() {
        $this->subdomain = $GLOBALS['SUBDOMAIN'];
        $this->username = $GLOBALS['USERNAME'];
        $this->password = $GLOBALS['PASSWORD'];
        $this->token = $GLOBALS['TOKEN'];
        $this->oAuthToken = $GLOBALS['OAUTH_TOKEN'];
        $this->client = new Client($this->subdomain, $this->username);
        $this->client->setAuth('token', $this->token);
    }

    public function testCredentials() {
        $this->assertEquals($GLOBALS['SUBDOMAIN'] != '', true, 'Expecting GLOBALS[SUBDOMAIN] parameter; does phpunit.xml exist?');
        $this->assertEquals($GLOBALS['TOKEN'] != '', true, 'Expecting GLOBALS[TOKEN] parameter; does phpunit.xml exist?');
        $this->assertEquals($GLOBALS['USERNAME'] != '', true, 'Expecting GLOBALS[USERNAME] parameter; does phpunit.xml exist?');
    }

    public function testAuthToken() {
        $this->client->setAuth('token', $this->token);
        $requests = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAll() {
        $tickets = $this->client->suspendedTickets()->findAll();
        $this->assertEquals(is_object($tickets), true, 'Should return an object');
        $this->assertEquals(is_array($tickets->suspended_tickets), true, 'Should return an object containing an array called "suspended_tickets"');
        $this->assertGreaterThan(0, $tickets->suspended_tickets[0]->id, 'Returns a non-numeric id for suspended_tickets[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $ticket = $this->client->suspendedTicket(205526502)->find(); // don't delete view #210610071
        $this->assertEquals(is_object($ticket), true, 'Should return an object');
        $this->assertEquals(is_object($ticket->suspended_ticket), true, 'Should return an object called "suspended_ticket"');
        $this->assertGreaterThan(0, $ticket->suspended_ticket->id, 'Returns a non-numeric id for view');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testRecover() {
        $this->markTestSkipped(
            'The only way to recover a ticket is to suspend it first (but that would result in a suspended user too)'
        );
        $ticket = $this->client->suspendedTicket(210610081)->recover();
        $this->assertEquals(is_object($ticket), true, 'Should return an object');
        $this->assertEquals(is_object($ticket->ticket), true, 'Should return an object called "ticket"');
        $this->assertGreaterThan(0, $ticket->ticket->id, 'Returns a non-numeric id for ticket');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testDelete() {
        $this->markTestSkipped(
            'The only way to delete a suspended ticket is to suspend it first (but that would result in a suspended user too)'
        );
        $ticket = $this->client->suspendedTicket(210610081)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
