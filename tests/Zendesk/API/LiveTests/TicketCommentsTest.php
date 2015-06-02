<?php

namespace Zendesk\API\LiveTests;

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
        $this->mockApiCall('GET', '/tickets/12345/comments.json?',
          array(
            'comments' => array(
                array(
                    'id' => 1
                )
            )
          )
        );

        $comments = $this->client->ticket($this->ticket_id)->comments()->findAll();
        $this->assertEquals(is_object($comments), true, 'Should return an object');
        $this->assertEquals(is_array($comments->comments), true,
            'Should return an object containing an array called "comments"');
        $this->assertGreaterThan(0, $comments->comments[0]->id, 'Returns a non-numeric id in first audit');
    }

    /*
     * Test make private
     */
    public function testMakePrivate()
    {
        $this->mockApiCall('GET', '/tickets/12345/comments.json?',
          array(
            'comments' => array(
              array(
                'id' => 1
              )
            )
          )
        );
        $comment_id = $this->client->ticket($this->ticket_id)->comments()->findAll()->comments[0]->id;

        $this->mockApiCall('PUT', '/tickets/12345/comments/1/make_private.json', array());
        $this->client->ticket($this->ticket_id)->comments($comment_id)->makePrivate();
    }
}
