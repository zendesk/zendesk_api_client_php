<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

/**
 * Tickets test class
 */
class TicketsTest extends BasicTest
{
    protected $testTicket;
    protected $testTicket2;

    public function setUp()
    {
        $this->testTicket = [
            'subject'  => 'The quick brown fox jumps over the lazy dog',
            'comment'  => [
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor'
                          . ' incididunt ut labore et dolore magna aliqua.'
            ],
            'priority' => 'normal',
            'id'       => '12345'
        ];

        $this->testTicket2 = [
            'subject'  => 'The second ticket',
            'comment'  => [
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor'
                          . ' incididunt ut labore et dolore magna aliqua.'
            ],
            'priority' => 'normal',
            'id'       => '4321'
        ];

        parent::setUp();
    }

    public function testAllSideLoadedMethod()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets()->sideload(['users', 'groups'])->findAll();

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'tickets.json',
                'queryParams' => ['include' => 'users,groups'],
            ]
        );

        $this->assertNull($this->client->getSideload());
    }

    public function testAllSideLoadedParameter()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets()->findAll(['sideload' => ['users', 'groups']]);

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'tickets.json',
                'queryParams' => ['include' => 'users,groups'],
            ]
        );
    }

    public function testFindSingle()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets()->find($this->testTicket['id']);

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'tickets/' . $this->testTicket['id'] . '.json',
            ]
        );

    }

    public function testFindSingleChainPattern()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets($this->testTicket['id'])->find();

        $this->assertLastRequestIs(
            [
                'method' => 'GET',
                'tickets/' . $this->testTicket['id'] . '.json',
            ]
        );
    }

    public function testFindMultiple()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets()->findMany([$this->testTicket['id'], $this->testTicket2['id']]);

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'tickets/show_many.json',
                'queryParams' => ['ids' => implode(',', [$this->testTicket['id'], $this->testTicket2['id']])],
            ]
        );
    }

    public function testFindMultipleWithSideload()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets()->findMany(
            [$this->testTicket['id'], $this->testTicket2['id']],
            ['sideload' => ['users', 'groups'], 'per_page' => 20],
            'ids'
        );

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'tickets/show_many.json',
                'queryParams' => [
                    'ids'      => implode(',', [$this->testTicket['id'], $this->testTicket2['id']]),
                    'per_page' => 20,
                    'include'  => 'users,groups'
                ],
            ]
        );
    }

    public function testDeleteMultiple()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets()->deleteMany([123, 321]);
        $this->assertLastRequestIs(
            [
                'method'      => 'DELETE',
                'endpoint'    => 'tickets/destroy_many.json',
                'queryParams' => ['ids' => implode(',', [123, 321])],
            ]
        );
    }

    public function testCreateWithAttachment()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['upload' => ['token' => 'asdf']])),
            new Response(200, [], json_encode(['ticket' => ['id' => '123']])),
        ]);

        $attachmentData = [
            'file' => getcwd() . '/tests/assets/UK.png',
            'name' => 'UK test non-alpha chars.png'
        ];

        $this->client->tickets()->attach($attachmentData)->create($this->testTicket);

        $this->assertRequestIs(
            [
                'method'      => 'POST',
                'endpoint'    => 'uploads.json',
                'queryParams' => ['filename' => rawurlencode($attachmentData['name'])],
                'file'        => $attachmentData['file'],
            ],
            0
        );

        $postFields = [
            'ticket' => [
                'subject'  => $this->testTicket['subject'],
                'comment'  => array_merge($this->testTicket['comment'], ['uploads' => ['asdf']]),
                'priority' => $this->testTicket['priority'],
                'id'       => $this->testTicket['id'],

            ]
        ];
        array_merge([$this->testTicket, ['uploads' => ['asdf']]]);
        $this->assertLastRequestIs([
            'method'     => 'POST',
            'endpoint'   => 'tickets.json',
            'postFields' => $postFields,
        ]);
    }

    public function testExport()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets()->export(['start_time' => '1332034771']);

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'exports/tickets.json',
                'queryParams' => ['start_time' => '1332034771'],
            ]
        );
    }

    public function testUpdateManyWithQueryParams()
    {
        $ticketIds = [$this->testTicket['id'], $this->testTicket2['id']];

        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets()->updateMany(
            [
                'ids'    => $ticketIds,
                'status' => 'solved'
            ]
        );

        $this->assertLastRequestIs(
            [
                'method'      => 'PUT',
                'endpoint'    => 'tickets/update_many.json',
                'queryParams' => ['ids' => implode(',', $ticketIds)],
                'postFields'  => ['ticket' => ['status' => 'solved']]
            ]
        );
    }

    public function testUpdateMany()
    {
        $tickets = [$this->testTicket, $this->testTicket2];

        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets()->updateMany($tickets);

        $this->assertLastRequestIs(
            [
                'method'     => 'PUT',
                'endpoint'   => 'tickets/update_many.json',
                'postFields' => ['tickets' => $tickets]
            ]
        );
    }

    public function testRelated()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['topic_id' => 1]))
        ]);

        $related = $this->client->tickets(12345)->related();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'tickets/12345/related.json',
            ]
        );

        // Test if the method returns readable data
        $this->assertEquals(is_object($related), true, 'Should return an object');
    }

    public function testCollaborators()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['topic_id' => 1]))
        ]);

        $collaborators = $this->client->tickets()->collaborators(['id' => 12345]);

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'tickets/12345/collaborators.json',
            ]
        );

        $this->assertEquals(is_object($collaborators), true, 'Should return an object');
    }

    public function testIncidents()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['topic_id' => 1]))
        ]);

        $incidents = $this->client->tickets()->incidents(['id' => 12345]);

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'tickets/12345/incidents.json',
            ]
        );

        $this->assertEquals(is_object($incidents), true, 'Should return an object');
    }

    public function testProblems()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['tickets' => []]))
        ]);

        $problems = $this->client->tickets()->problems();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'problems.json',
            ]
        );

        $this->assertEquals(is_object($problems), true, 'Should return an object');
    }

    public function testProblemAutoComplete()
    {
        $this->mockAPIResponses([
            new Response(200, [], json_encode(['tickets' => []]))
        ]);

        $this->client->tickets()->problemAutoComplete(['text' => 'foo']);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => 'problems/autocomplete.json',
                'postFields' => ['text' => 'foo'],
            ]
        );

    }

    public function testMarkAsSpam()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets(12345)->markAsSpam();

        $this->assertLastRequestIs(
            [
                'method'   => 'PUT',
                'endpoint' => 'tickets/12345/mark_as_spam.json',
            ]
        );
    }

    public function testMarkManyAsSpam()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->tickets()->markAsSpam([12345, 54321]);

        $this->assertLastRequestIs(
            [
                'method'      => 'PUT',
                'endpoint'    => 'tickets/mark_many_as_spam.json',
                'queryParams' => ['ids' => '12345,54321']
            ]
        );
    }
}
