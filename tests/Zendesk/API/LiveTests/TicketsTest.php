<?php

namespace Zendesk\API\LiveTests;

/**
 * Tickets test class
 */
class TicketsTest extends BasicTest
{
    /**
     * Test creating of ticket
     */
    public function testCreateTicket()
    {
        $ticketParams = [
            'subject'  => 'The quick brown fox jumps over the lazy dog',
            'comment'  => [
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor'
                          . ' incididunt ut labore et dolore magna aliqua.'
            ],
            'priority' => 'normal',
        ];
        $response     = $this->client->tickets()->create($ticketParams);
        $this->assertNotNull($response);
        $this->assertTrue(property_exists($response, 'ticket'));
        $ticket = $response->ticket;
        $this->assertEquals($ticketParams['subject'], $ticket->subject);
        $this->assertEquals($ticketParams['comment']['body'], $ticket->description);
        $this->assertEquals($ticketParams['priority'], $ticket->priority);
        $this->assertNotNull($ticket->id);

        return $ticket;
    }

    /**
     * Tests if the client can call and build the tickets endpoint with the proper sideloads
     *
     * @depends testCreateTicket
     */
    public function testFindAll($ticket)
    {
        $this->assertTrue(true);
        $response = $this->client->tickets()->findAll();
        $this->assertTrue(property_exists($response, 'tickets'));
        $this->assertGreaterThan(0, count($response->tickets));
    }

    /**
     * Tests if the client can call and build the find ticket endpoint
     *
     * @depends testCreateTicket
     */
    public function testFindSingle($ticket)
    {
        $response = $this->client->tickets()->find($ticket->id);

        foreach (['id', 'subject', 'description', 'priority'] as $property) {
            $this->assertTrue(property_exists($response->ticket, $property));
            $this->assertEquals($ticket->$property, $response->ticket->$property);
        }
    }

    /**
     * Tests if the client can call and build the show many tickets endpoint with the correct IDs
     *
     * @depends testCreateTicket
     */
    public function testFindMultiple($ticket)
    {
        $ticket2 = $this->createTestTicket();

        $response = $this->client->tickets()->findMany([$ticket->id, $ticket2->id]);
        $this->assertTrue(property_exists($response, 'tickets'));
        $this->assertEquals(2, count($response->tickets));
        $this->assertEquals(2, $response->count);
    }

    /**
     * Tests if the client can update tickets
     *
     * @depends testCreateTicket
     */
    public function testUpdate($ticket)
    {
        $ticketParams  = [
            'subject'  => 'The new subject',
            'priority' => 'high',
        ];
        $updatedTicket = $this->client->tickets()->update($ticket->id, $ticketParams);
        $this->assertEquals(
            $ticketParams['subject'],
            $updatedTicket->ticket->subject,
            'Should have updated ticket subject.'
        );
        $this->assertEquals(
            $ticketParams['priority'],
            $updatedTicket->ticket->priority,
            'Should have updated ticket priority.'
        );
    }

    /**
     * Tests if the client can call and build the create ticket witch attachment endpoint and initiate the file upload
     * headers and POST data
     */
    public function testCreateWithAttachment()
    {
        $attachmentData = [
            'file' => getcwd() . '/tests/assets/UK.png',
            'name' => 'UK test non-alpha chars.png'
        ];

        $ticketParams = [
            'subject'  => 'This is a sample subject of a ticket with attachment',
            'comment'  => [
                'body' => 'Your body is a wonderland'
            ],
            'priority' => 'normal',
        ];

        $response = $this->client->tickets()->attach($attachmentData)->create($ticketParams);

        $this->assertNotNull($ticket = $response->ticket);
        $this->assertEquals($ticketParams['subject'], $ticket->subject);
        $this->assertEquals($ticketParams['comment']['body'], $ticket->description);
        $this->assertEquals($ticketParams['priority'], $ticket->priority);

        $attachmentFound = false;
        foreach ($response->audit->events as $event) {
            if (property_exists($event, 'attachments')) {
                $attachmentFound = true;
                break;
            }
        }
        $this->assertTrue($attachmentFound, 'Should have attachment in ticket audits.');

        return $ticket;
    }

    /**
     * Tests if the client can call and build the update many tickets endpoint with the correct IDS and POST fields
     *
     * @depends testCreateTicket
     */
    public function testUpdateManyWithQueryParams($ticket)
    {
        $ticket2 = $this->createTestTicket();

        $ticketIds = [$ticket->id, $ticket2->id];

        $updatedTickets = $this->client->tickets()->updateMany(
            [
                'ids'    => $ticketIds,
                'status' => 'solved'
            ]
        );

        $this->assertTrue(property_exists($updatedTickets, 'job_status'));
        $this->assertEquals(
            'queued',
            $updatedTickets->job_status->status,
            'Should have queued the multiple update task'
        );
    }

    /**
     * Tests if the client can call and build the related tickets endpoint with the correct ID
     *
     * @depends testCreateTicket
     */
    public function testRelated($ticket)
    {
        $response = $this->client->tickets($ticket->id)->related();

        $properties = [
            'url',
            'topic_id',
            'jira_issue_ids',
            'followup_source_ids',
            'from_archive',
            'incidents',
            'twitter'
        ];
        foreach ($properties as $property) {
            $this->assertTrue(
                property_exists($response->ticket_related, $property),
                'Should have property ' . $property
            );
        }
    }

    /**
     * Tests if the client can call and build the ticket collaborators endpoint with the correct ID
     *
     * @depends testCreateTicket
     */
    public function testCollaborators($ticket)
    {
        $collaborators = $this->client->tickets()->collaborators(['id' => $ticket->id]);
        $this->assertTrue(property_exists($collaborators, 'users'), 'Should return users.');
    }

    /**
     * Tests if the client can call and build the tickets incidents endpoint with the correct ID
     *
     * @depends testCreateTicket
     */
    public function testIncidents($ticket)
    {
        $incidents = $this->client->tickets($ticket->id)->incidents();
        $this->assertTrue(property_exists($incidents, 'tickets'), 'Should return tickets.');
    }

    /**
     * Tests if the client can call and build the delete tickets endpoint
     * This will throw an exception if it fails
     *
     * @depends testCreateWithAttachment
     */
    public function testDelete($ticket)
    {
        try {
            $this->client->tickets($ticket->id)->delete();
        } catch (\Exception $e) {
            $this->fail('An exception was not expected. Exception thrown was ' . $e->__toString());
        }
    }

    /**
     * Tests if the client can call and build the delete many tickets endpoint with the correct IDs
     *
     * @depends testCreateTicket
     */
    public function testDeleteMultiple($ticket)
    {
        $ticket2 = $this->createTestTicket();

        $response = $this->client->tickets()->deleteMany([$ticket->id, $ticket2->id]);

        $this->assertTrue(property_exists($response, 'job_status'));
        $this->assertEquals(
            'queued',
            $response->job_status->status,
            'Should have queued the multiple delete task'
        );
    }

    /**
     * Create a test ticket
     *
     * @return mixed
     */
    private function createTestTicket()
    {
        $ticketParams = [
            'subject'  => 'The fox jumps over the dog again.' . microtime(),
            'comment'  => [
                'body' => 'Lorem ipsum dolor sit amet' . microtime()
            ],
            'priority' => 'low',
        ];
        $response     = $this->client->tickets()->create($ticketParams);

        return $response->ticket;
    }
}
