<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

/**
 * Ticket Comments test class
 */
class TicketCommentsTest extends BasicTest
{
    protected $ticket_id;

    public function setUp()
    {
        $this->testTicket = array(
            'id' => "12345",
            'subject' => 'Ticket comment test',
            'comment' => array(
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ),
            'priority' => 'normal'
        );
        $this->ticket_id = $this->testTicket['id'];

        parent::setUp();
    }

    public function testAll()
    {
        $this->mockAPIResponses([
          new Response(200, [], json_encode(['comments' => [['id' => 1]]]))
        ]);

        $comments = $this->client->tickets($this->ticket_id)->comments()->findAll();

        $this->assertLastRequestIs(
            [
            'method' => 'GET',
            'endpoint' => 'tickets/12345/comments.json',
            ]
        );

        $this->assertEquals(is_object($comments), true, 'Should return an object');
        $this->assertEquals(
            is_array($comments->comments),
            true,
            'Should return an object containing an array called "comments"'
        );
        $this->assertGreaterThan(0, $comments->comments[0]->id, 'Should return a numeric ID.');
    }

    /*
     * Test make private
     */
    public function testMakePrivate()
    {
        $this->mockAPIResponses([
          new Response(200, [], '')
        ]);

        $this->client->tickets(12345)->comments(1)->makePrivate();

        $this->assertLastRequestIs(
            [
            'method' => 'PUT',
            'endpoint' => 'tickets/12345/comments/1/make_private.json',
            ]
        );
    }
}
