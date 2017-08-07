<?php

namespace Zendesk\API\LiveTests;

use Faker\Factory;

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
        $faker        = Factory::create();
        $ticketParams = [
            'subject'  => $faker->sentence(5),
            'comment'  => [
                'body' => $faker->sentence(10),
            ],
            'priority' => 'normal',
        ];
        $response     = $this->client->tickets()->create($ticketParams);
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
        $faker         = factory::create();
        $ticketParams  = [
            'subject'  => $faker->sentence(3),
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

        $faker        = factory::create();
        $ticketParams = [
            'subject'  => $faker->sentence(5),
            'comment'  => [
                'body' => $faker->sentence(10),
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
        $this->assertTrue(property_exists($collaborators, 'users'), 'Should find the collaborators on a ticket.');
    }

    /**
     * Tests if the client can call and build the tickets incidents endpoint with the correct ID
     */
    public function testIncidents()
    {
        $problemTicket  = $this->createTestTicket(['type' => 'problem']);
        $incidentTicket = $this->createTestTicket(['type' => 'incident', 'problem_id' => $problemTicket->id]);
        $incidents      = $this->client->tickets($problemTicket->id)->incidents();
        $this->assertTrue(
            property_exists($incidents, 'tickets'),
            'Should find the incident tickets associated to a problem ticket.'
        );

        $this->assertNotNull($incident = $incidents->tickets[0]);
        $this->assertEquals($incidentTicket->id, $incident->id);
        $this->assertEquals($incidentTicket->subject, $incident->subject);
    }

    /**
     * Tests if the client can call and build the delete tickets endpoint
     * This will throw an exception if it fails
     *
     * @depends testCreateWithAttachment
     */
    public function testDelete($ticket)
    {
        $this->client->tickets($ticket->id)->delete();
        $this->assertEquals(204, $this->client->getDebug()->lastResponseCode);
        $this->assertNull($this->client->getDebug()->lastResponseError);
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
     * @param array $extraParams
     *
     * @return mixed
     */
    private function createTestTicket($extraParams = [])
    {
        $faker        = Factory::create();
        $ticketParams = array_merge([
            'subject'  => $faker->sentence(5),
            'comment'  => [
                'body' => $faker->sentence(10),
            ],
            'priority' => 'low',
        ], $extraParams);
        $response     = $this->client->tickets()->create($ticketParams);

        return $response->ticket;
    }

    /**
     * Test we can handle api exceptions, by finding a non-existing ticket
     *
     * @expectedException Zendesk\API\Exceptions\ApiResponseException
     * @expectedExceptionMessage Not Found
     */
    public function testHandlesApiException()
    {
        $this->client->tickets()->find(99999999);
    }

    /**
     * Test if a ticket with a group_id is assigned to the correct group.
     */
    public function testAssignTicketToGroup()
    {
        $faker = Factory::create();
        $group = $this->client->groups()->create(['name' => $faker->word])->group;

        $ticket = $this->createTestTicket([
            'group_id' => $group->id,
            'type' => 'problem',
            'tags' => ['testing', 'api']
        ]);

        $this->assertEquals($group->id, $ticket->group_id);

        $this->client->groups()->delete($group->id);
        $this->client->tickets()->delete($ticket->id);
    }

    /**
     * Test if tags are updated on ticket updated.
     *
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testTagsAdded()
    {
        $faker = Factory::create();

        $tags = $faker->words(10);

        $ticket = $this->createTestTicket();
        $this->client->tickets($ticket->id)->tags()->update(null, $tags);

        $updatedTicket = $this->client->tickets()->find($ticket->id);

        $this->assertEmpty(array_diff($tags, $updatedTicket->ticket->tags), 'Tags should be updated.');

        $this->client->tickets()->delete($ticket->id);
    }
}
