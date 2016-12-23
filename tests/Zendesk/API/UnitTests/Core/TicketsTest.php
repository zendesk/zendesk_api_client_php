<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\UnitTests\BasicTest;

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

    /**
     * Tests if the client can call and build the tickets endpoint with the proper sideloads
     */
    public function testAllSideLoadedMethod()
    {
        $this->assertEndpointCalled(function () {
            $this->client->tickets()->sideload(['users', 'groups'])->findAll();
        }, 'tickets.json', 'GET', ['queryParams' => ['include' => 'users,groups']]);

        $this->assertNull($this->client->getSideload());
    }

    /**
     * Tests if the client can call and build the tickets endpoint with the proper sideloads
     */
    public function testAllSideLoadedParameter()
    {
        $this->assertEndpointCalled(function () {
            $this->client->tickets()->findAll(['sideload' => ['users', 'groups']]);
        }, 'tickets.json', 'GET', ['queryParams' => ['include' => 'users,groups']]);
    }

    /**
     * Tests if the client can call and build the find ticket endpoint
     */
    public function testFindSingle()
    {
        $this->assertEndpointCalled(function () {
            $this->client->tickets()->find($this->testTicket['id']);
        }, 'tickets/' . $this->testTicket['id'] . '.json');
    }

    /**
     * Tests if the client can call and build the find ticket endpoint while chaining
     */
    public function testFindSingleChainPattern()
    {
        $this->assertEndpointCalled(function () {
            $this->client->tickets($this->testTicket['id'])->find();
        }, 'tickets/' . $this->testTicket['id'] . '.json');
    }

    /**
     * Tests if the client can call and build the show many tickets endpoint with the correct IDs
     */
    public function testFindMultiple()
    {
        $this->assertEndpointCalled(
            function () {
                $this->client->tickets()->findMany([$this->testTicket['id'], $this->testTicket2['id']]);
            },
            'tickets/show_many.json',
            'GET',
            [
                'queryParams' => [
                    'ids' => implode(',', [$this->testTicket['id'], $this->testTicket2['id']])
                ]
            ]
        );
    }

    /**
     * Tests if the client can call and build the show many tickets endpoint with the proper sideloads and correct IDs
     */
    public function testFindMultipleWithSideload()
    {
        $this->assertEndpointCalled(
            function () {
                $this->client->tickets()->findMany(
                    [$this->testTicket['id'], $this->testTicket2['id']],
                    ['sideload' => ['users', 'groups'], 'per_page' => 20],
                    'ids'
                );
            },
            'tickets/show_many.json',
            'GET',
            [
                'queryParams' => [
                    'ids'      => implode(',', [$this->testTicket['id'], $this->testTicket2['id']]),
                    'per_page' => 20,
                    'include'  => 'users,groups'
                ]
            ]
        );
    }

    /**
     * Tests if the client can call and build the delete many tickets endpoint with the correct IDs
     */
    public function testDeleteMultiple()
    {
        $this->assertEndpointCalled(function () {
            $this->client->tickets()->deleteMany([123, 321]);
        }, 'tickets/destroy_many.json', 'DELETE', ['queryParams' => ['ids' => implode(',', [123, 321])]]);
    }

    /**
     * Tests if the client can call and build the create ticket witch attachment endpoint and initiate the file upload
     * headers and POST data
     */
    public function testCreateWithAttachment()
    {
        $this->mockApiResponses([
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
    
    /**
     * Tests that we can create the ticket with an async parameter which will add `async=true` to the query parameters
     */
    public function testCreateAsync()
    {
        $this->mockApiResponses([
            new Response(200, [], json_encode(['ticket' => ['id' => '123']])),
        ]);

        $this->testTicket['async'] = true;
        $this->client->tickets()->create($this->testTicket);

        $this->assertLastRequestIs([
            'method'     => 'POST',
            'endpoint'   => 'tickets.json',
            'queryParams' => ['async' => true],
        ]);
    }

    /**
     * Tests if the client can call and build the export tickets endpoint with the proper pagination query
     */
    public function testExport()
    {
        $this->assertEndpointCalled(function () {
            $this->client->tickets()->export(['start_time' => '1332034771']);
        }, 'exports/tickets.json', 'GET', ['queryParams' => ['start_time' => '1332034771']]);
    }

    /**
     * Tests if the client can call and build the update many tickets endpoint with the correct IDS and POST fields
     */
    public function testUpdateManyWithQueryParams()
    {
        $ticketIds = [$this->testTicket['id'], $this->testTicket2['id']];

        $this->assertEndpointCalled(
            function () use ($ticketIds) {
                $this->client->tickets()->updateMany(
                    [
                        'ids'    => $ticketIds,
                        'status' => 'solved'
                    ]
                );
            },
            'tickets/update_many.json',
            'PUT',
            [
                'queryParams' => ['ids' => implode(',', $ticketIds)],
                'postFields'  => ['ticket' => ['status' => 'solved']]
            ]
        );
    }

    /**
     * Tests if the client can call and build the update many tickets endpoint with the correct IDS and POST fields
     */
    public function testUpdateMany()
    {
        $tickets = [$this->testTicket, $this->testTicket2];

        $this->assertEndpointCalled(function () use ($tickets) {
            $this->client->tickets()->updateMany($tickets);
        }, 'tickets/update_many.json', 'PUT', ['postFields' => ['tickets' => $tickets]]);
    }

    /**
     * Tests if the client can call and build the related tickets endpoint with the correct ID
     */
    public function testRelated()
    {
        $this->assertEndpointCalled(function () {
            $this->client->tickets(12345)->related();
        }, 'tickets/12345/related.json');
    }

    /**
     * Tests if the client can call and build the ticket collaborators endpoint with the correct ID
     */
    public function testCollaborators()
    {
        $this->assertEndpointCalled(function () {
            $this->client->tickets()->collaborators(['id' => 12345]);
        }, 'tickets/12345/collaborators.json');
    }

    /**
     * Tests if the client can call and build the tickets incidents endpoint with the correct ID
     */
    public function testIncidents()
    {
        $this->assertEndpointCalled(function () {
            $this->client->tickets()->incidents(['id' => 12345]);
        }, 'tickets/12345/incidents.json');
    }

    /**
     * Tests if the client can call and build the problem tickets endpoint
     */
    public function testProblems()
    {
        $this->assertEndpointCalled(function () {
            $this->client->tickets()->problems();
        }, 'problems.json');
    }

    /**
     * Tests if the client can call and build the problem autocomplete endpoint
     */
    public function testProblemAutoComplete()
    {
        $this->assertEndpointCalled(function () {
            $this->client->tickets()->problemAutoComplete(['text' => 'foo']);
        }, 'problems/autocomplete.json', 'POST', ['postFields' => ['text' => 'foo']]);
    }

    /**
     * Tests if the client can call and build the mark ticket as spam endpoint with the correct ID
     */
    public function testMarkAsSpam()
    {
        $this->assertEndpointCalled(function () {
            $this->client->tickets(12345)->markAsSpam();
        }, 'tickets/12345/mark_as_spam.json', 'PUT');
    }

    /**
     * Tests if the client can call and build the mark many tickets as spam endpoint with the correct IDs
     */
    public function testMarkManyAsSpam()
    {
        $this->assertEndpointCalled(function () {
            $this->client->tickets()->markAsSpam([12345, 54321]);
        }, 'tickets/mark_many_as_spam.json', 'PUT', ['queryParams' => ['ids' => '12345,54321']]);
    }

    /**
     * Tests if the client can call the merge endpoint.
     */
    public function testMerge()
    {
        $params   = [
            'ids'            => [123, 234],
            'target_comment' => 'Closing in favor of #345',
            'source_comment' => 'Combining with #123, #234',
        ];
        $ticketId = 345;
        $this->assertEndpointCalled(function () use ($ticketId, $params) {
            $this->client->tickets($ticketId)->merge($params);
        }, "tickets/{$ticketId}/merge.json", 'POST', $params);
    }
}
