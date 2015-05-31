<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Tickets test class
 */
class TicketsTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
        $this->assertEquals($this->password != '', true, 'Expecting $this->password parameter; does phpunit.xml exist?');
        //$this->assertEquals($_ENV['OAUTH_TOKEN'] != '', true, 'Expecting _ENV[OAUTH_TOKEN] parameter; does phpunit.xml exist?');
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    public function testAuthPassword() {
        $this->client->setAuth('password', $this->password);
        $tickets = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testAuthOAuth() {
        $this->markTestSkipped(
            'Sskip this test first, because it needs an OAth Token'
        );
        $this->client->setAuth('oauth_token', $this->oAuthToken);
        $tickets = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    protected $testTicket;

    public function setUP() {
        $this->testTicket = array(
            'subject' => 'The quick brown fox jumps over the lazy dog',
            'comment' => array (
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ),
            'priority' => 'normal'
        );
        $ticket = $this->client->tickets()->create($this->testTicket);
        $this->assertEquals(is_object($ticket), true, 'Should return an object');
        $this->assertEquals(is_object($ticket->ticket), true, 'Should return an object called "ticket"');
        $this->assertGreaterThan(0, $ticket->ticket->id, 'Returns a non-numeric id for ticket');
        $this->assertEquals($ticket->ticket->subject, $this->testTicket['subject'], 'Subject of test ticket does not match');
        $this->assertEquals($ticket->ticket->description, $this->testTicket['comment']['body'], 'Description of test ticket does not match');
        $this->assertEquals($ticket->ticket->priority, $this->testTicket['priority'], 'Priority of test ticket does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->testTicket['id'] = $ticket->ticket->id;
    }

    public function testAll() {
        $tickets = $this->client->tickets()->findAll();
        $this->assertEquals(is_object($tickets), true, 'Should return an object');
        $this->assertEquals(is_array($tickets->tickets), true, 'Should return an object containing an array called "tickets"');
        $this->assertGreaterThan(0, $tickets->tickets[0]->id, 'Returns a non-numeric id in first ticket');
        $this->assertContains($tickets->tickets[0]->priority, array (null, 'low', 'normal', 'high', 'urgent'), 'Returns an invalid priority in first ticket');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testAllSideLoadedMethod() {
        $tickets = $this->client->tickets()->sideload(array('users', 'groups'))->findAll();
        $this->assertEquals(is_object($tickets), true, 'Should return an object');
        $this->assertEquals(is_array($tickets->users), true, 'Should return an object containing an array called "users"');
        $this->assertEquals(is_array($tickets->groups), true, 'Should return an object containing an array called "groups"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testAllSideLoadedParameter() {
        $tickets = $this->client->tickets()->findAll(array('sideload' => array('users', 'groups')));
        $this->assertEquals(is_object($tickets), true, 'Should return an object');
        $this->assertEquals(is_array($tickets->users), true, 'Should return an object containing an array called "users"');
        $this->assertEquals(is_array($tickets->groups), true, 'Should return an object containing an array called "groups"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFindSingle() {
        $tickets = $this->client->ticket($this->testTicket['id'])->find();
        $this->assertEquals(is_object($tickets->ticket), true, 'Should return an object called "ticket"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFindMultiple() {
        $testTicket = array(
            'subject' => 'The second ticket',
            'comment' => array (
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ),
            'priority' => 'normal'
        );
        $ticket2 = $this->client->tickets()->create($testTicket);

        $tickets = $this->client->tickets(array($this->testTicket['id'], $ticket2->ticket->id))->find();
        $this->assertEquals(is_object($tickets), true, 'Should return an object');
        $this->assertEquals(is_array($tickets->tickets), true, 'Should return an array called "tickets"');
        $this->assertEquals(is_object($tickets->tickets[0]), true, 'Should return an object as first "tickets" array element');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $this->client->ticket($ticket2->ticket->id)->delete();
    }

    public function testUpdate() {
        $testTicket = $this->testTicket;
        $this->assertGreaterThan(0, $testTicket['id'], 'Cannot find a ticket id to test with. Did setUp fail?');
        $testTicket['subject'] = 'Updated subject';
        $testTicket['priority'] = 'urgent';
        $ticket = $this->client->tickets()->update($testTicket);
        $this->assertEquals(is_object($ticket->ticket), true, 'Should return an object called "ticket"');
        $this->assertGreaterThan(0, $ticket->ticket->id, 'Returns a non-numeric id for ticket');
        $this->assertEquals($ticket->ticket->subject, $testTicket['subject'], 'Subject of test ticket does not match');
        $this->assertEquals($ticket->ticket->description, $testTicket['comment']['body'], 'Description of test ticket does not match');
        $this->assertEquals($ticket->ticket->priority, $testTicket['priority'], 'Priority of test ticket does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testDeleteMultiple() {
        // Assume setUp works so we can go ahead and create two new tickets
        $testTicket = array(
            'subject' => 'The quick brown fox jumps over the lazy dog',
            'comment' => array (
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ),
            'priority' => 'normal'
        );
        $ticket1 = $this->client->tickets()->create($this->testTicket);
        $this->assertEquals(is_object($ticket1), true, 'Ticket1: Should return an object');
        $this->assertEquals(is_object($ticket1->ticket), true, 'Ticket1: Should return an object called "ticket"');
        $this->assertGreaterThan(0, $ticket1->ticket->id, 'Ticket1: Returns a non-numeric id for ticket');
        $ticket2 = $this->client->tickets()->create($testTicket);
        $this->assertEquals(is_object($ticket2), true, 'Ticket2: Should return an object');
        $this->assertEquals(is_object($ticket2->ticket), true, 'Ticket2: Should return an object called "ticket"');
        $this->assertGreaterThan(0, $ticket2->ticket->id, 'Ticket2: Returns a non-numeric id for ticket');
        // Test delete
        $this->client->tickets(array($ticket1->ticket->id, $ticket2->ticket->id))->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testCreateWithAttachment() {
        $testTicket = array(
            'subject' => 'The quick brown fox jumps over the lazy dog',
            'comment' => array (
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ),
            'priority' => 'normal'
        );
        $ticket = $this->client->tickets()->attach(array(
            'file' => getcwd().'/tests/assets/UK.png',
            'name' => 'UK test non-alpha chars.png'
        ))->create($testTicket);
        $this->assertEquals(is_object($ticket), true, 'Should return an object');
        $this->assertEquals(is_object($ticket->ticket), true, 'Should return an object called "ticket"');
        $this->assertGreaterThan(0, $ticket->ticket->id, 'Returns a non-numeric id for ticket');
        $this->assertEquals(is_array($ticket->audit->events), true, 'Should return an array called "audit->events"');
        $this->assertEquals(is_array($ticket->audit->events[0]->attachments), true, 'Should return an array called "audit->events->attachments"');
        $this->assertGreaterThan(0, count($ticket->audit->events[0]->attachments), 'Attachment count should be above zero');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Create does not return HTTP code 201');
        $this->client->ticket($ticket->ticket->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Delete does not return HTTP code 200');
    }

    public function testExport() {
        $tickets = $this->client->tickets()->export(array('start_time' => '1332034771'));
        $this->assertEquals(is_object($tickets), true, 'Should return an object');
        $this->assertEquals(is_array($tickets->results), true, 'Should return an object containing an array called "results"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testIncremental() {
        $tickets = $this->client->tickets()->incremental(array('start_time' => '1332034771'));
        $this->assertEquals(is_object($tickets), true, 'Should return an object');
        $this->assertEquals(is_array($tickets->tickets), true, 'Should return an object containing an array called "tickets"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testCreateFromTweet() {
        $this->markTestSkipped(
            'Skipped for now because it requires a new (unique) twitter id for each test'
        );
        $twitter_id = $this->client->twitter()->handles()->monitored_twitter_handles[0]->id;
        $params = array(
            'monitored_twitter_handle_id' => $twitter_id,
            'twitter_status_message_id' => '419191717649473536'
        );
        $ticket = $this->client->tickets()->createFromTweet($params);
        $this->assertEquals(is_object($ticket), true, 'Should return an object');
        $this->assertEquals(is_object($ticket->ticket), true, 'Should return an object called "ticket"');
        $this->assertGreaterThan(0, $ticket->ticket->id, 'Returns a non-numeric id for ticket');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Create does not return HTTP code 201');
        $this->client->tickets->delete(array('id' => $ticket->ticket->id));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Delete does not return HTTP code 200');
    }

    public function tearDown() {
        $testTicket = $this->testTicket;
        $this->assertGreaterThan(0, $testTicket['id'], 'Cannot find a ticket id to test with. Did setUp fail?');
        $ticket = $this->client->ticket($testTicket['id'])->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}
