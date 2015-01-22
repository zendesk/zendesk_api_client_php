<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Ticket Comments test class
 */
class TicketCommentsTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    protected $ticket_id;

    public function setUp(){
        $testTicket = array(
            'subject' => 'Ticket comment test',
            'comment' => array (
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ),
            'priority' => 'normal'
        );
        $ticket = $this->client->tickets()->create($testTicket);
        $this->ticket_id = $ticket->ticket->id;
    }

    public function testAll() {
        $comments = $this->client->ticket($this->ticket_id)->comments()->findAll();
        $this->assertEquals(is_object($comments), true, 'Should return an object');
        $this->assertEquals(is_array($comments->comments), true, 'Should return an object containing an array called "comments"');
        $this->assertGreaterThan(0, $comments->comments[0]->id, 'Returns a non-numeric id in first audit');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /*
     * Test make private
     */
    public function testMakePrivate() {
        $comment_id = $this->client->ticket($this->ticket_id)->comments()->findAll()->comments[0]->id;
        $comments = $this->client->ticket($this->ticket_id)->comments($comment_id)->makePrivate();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown(){
        $this->client->tickets($this->ticket_id)->delete();
    }

}
