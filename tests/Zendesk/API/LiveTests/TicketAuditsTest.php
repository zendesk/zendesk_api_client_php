<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Ticket Audits test class
 */
class TicketAuditsTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    protected $ticket_id;

    public function setUp(){
        $testTicket = array(
            'subject' => 'The quick brown fox jumps over the lazy dog',
            'comment' => array (
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ),
            'priority' => 'normal'
        );
        $ticket = $this->client->tickets()->create($testTicket);

        $update_testTicket['id'] = $ticket->ticket->id;
        $update_testTicket2['subject'] = 'Updated subject';
        $update_testTicket2['priority'] = 'urgent';
        $ticket = $this->client->tickets()->update($update_testTicket);

        $this->ticket_id = $ticket->ticket->id;

    }
    public function testAll() {
        $audits = $this->client->ticket($this->ticket_id)->audits()->findAll();
        $this->assertEquals(is_object($audits), true, 'Should return an object');
        $this->assertEquals(is_array($audits->audits), true, 'Should return an object containing an array called "audits"');
        $this->assertGreaterThan(0, $audits->audits[0]->id, 'Returns a non-numeric id in first audit');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testAllSideLoadedMethod() {
        $audits = $this->client->ticket($this->ticket_id)->sideload(array('users', 'groups'))->audits()->findAll();
        $this->assertEquals(is_object($audits), true, 'Should return an object');
        $this->assertEquals(is_array($audits->users), true, 'Should return an object containing an array called "users"');
        $this->assertEquals(is_array($audits->groups), true, 'Should return an object containing an array called "groups"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testAllSideLoadedParameter() {
        $audits = $this->client->ticket($this->ticket_id)->audits()->findAll(array('sideload' => array('users', 'groups')));
        $this->assertEquals(is_object($audits), true, 'Should return an object');
        $this->assertEquals(is_array($audits->users), true, 'Should return an object containing an array called "users"');
        $this->assertEquals(is_array($audits->groups), true, 'Should return an object containing an array called "groups"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind() {
        $audit_id = $this->client->ticket($this->ticket_id)->audits()->findAll()->audits[0]->id;
        $audits = $this->client->ticket($this->ticket_id)->audit($audit_id)->find();
        $this->assertEquals(is_object($audits), true, 'Should return an object');
        $this->assertEquals(is_object($audits->audit), true, 'Should return an object containing an array called "audit"');
        $this->assertEquals($audit_id, $audits->audit->id, 'Returns an incorrect id in audit object');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /*
     * Test mark as trusted. Need a voice comment or Facebook comment for this test
     */
    // public function testMarkAsTrusted() {
    //     $audits = $this->client->ticket(2)->audit(16317679361)->markAsTrusted();
    //     $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    // }

    public function tearDown(){
        $this->client->tickets($this->ticket_id)->delete();
    }

}
