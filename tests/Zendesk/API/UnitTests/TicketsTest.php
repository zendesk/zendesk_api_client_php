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
        $this->testTicket = array(
          'subject'  => 'The quick brown fox jumps over the lazy dog',
          'comment'  => array(
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
          ),
          'priority' => 'normal',
          'id'       => '12345'
        );

        $this->testTicket2 = array(
          'subject'  => 'The second ticket',
          'comment'  => array(
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
          ),
          'priority' => 'normal',
          'id'       => '4321'
        );

        parent::setUp();
    }

    public function testAllSideLoadedMethod()
    {
        $this->mockAPIResponses([
          new Response(200, [], '')
        ]);

        $this->client->tickets()->sideload(array( 'users', 'groups' ))->findAll();

        $this->assertLastRequestIs(
          [
            'method' => 'GET',
            'endpoint' => 'tickets.json',
            'queryParams' => ['include' => 'users,groups'],
          ]
        );
    }

    public function testAllSideLoadedParameter()
    {
        $this->mockAPIResponses([
          new Response(200, [], '')
        ]);

        $this->client->tickets()->findAll(array( 'sideload' => array( 'users', 'groups' ) ));

        $this->assertLastRequestIs(
          [
            'method' => 'GET',
            'endpoint' => 'tickets.json',
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
            'method' => 'GET',
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

        $testTicketIds = [ 'ids' => [ $this->testTicket['id'], $this->testTicket2['id'] ] ];
        $this->client->tickets()->findMany($testTicketIds);

        $this->assertLastRequestIs(
          [
            'method' => 'GET',
            'endpoint' => 'tickets/show_many.json',
            'queryParams' => ['ids' => implode(',', [ $this->testTicket['id'], $this->testTicket2['id'] ])],
          ]
        );
    }

    public function testDeleteMultiple()
    {
        $this->mockAPIResponses([
          new Response(200, [], '')
        ]);

        $this->client->tickets()->deleteMany(array( 123, 321 ));
        $this->assertLastRequestIs(
          [
            'method' => 'DELETE',
            'endpoint' => 'tickets/destroy_many.json',
            'queryParams' => ['ids' => implode(',', [ 123, 321 ])],
          ]
        );
    }

    public function testCreateWithAttachment()
    {
        $this->markTestSkipped('Waiting for side chaining to support this');

        $this->mockApiCall(
            'POST',
            '/uploads.json?filename=UK%20test%20non-alpha%20chars.png',
            array( 'upload' => array( 'token' => 'asdf' ) ),
            [ 'code' => 201 ]
        );

        $this->mockApiCall(
            'POST',
            'tickets.json',
            array( 'ticket' => array( 'id' => '123' ) ),
            array( 'code' => 201 )
        );

        $this->client->tickets()->attach(array(
          'file' => getcwd() . '/tests/assets/UK.png',
          'name' => 'UK test non-alpha chars.png'
        ))->create($this->testTicket);
        $this->httpMock->verify();
    }

    public function testExport()
    {
        $this->mockAPIResponses([
          new Response(200, [], '')
        ]);

        $this->client->tickets()->export(array( 'start_time' => '1332034771' ));

        $this->assertLastRequestIs(
          [
            'method' => 'GET',
            'endpoint' => 'exports/tickets.json',
            'queryParams' => [ 'start_time' => '1332034771' ],
          ]
        );
    }

    public function testUpdateManyWithQueryParams()
    {
        $ticketIds = [ $this->testTicket['id'], $this->testTicket2['id'] ];

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
            'method' => 'PUT',
            'endpoint' => 'tickets/update_many.json',
            'queryParams' => [ 'ids' => implode(',', $ticketIds) ],
            'postFields'  => [ 'ticket' => [ 'status' => 'solved' ] ]
          ]
        );
    }

    public function testUpdateMany()
    {
        $tickets = [ $this->testTicket, $this->testTicket2 ];

        $this->mockAPIResponses([
          new Response(200, [], '')
        ]);

        $this->client->tickets()->updateMany($tickets);

        $this->assertLastRequestIs(
          [
            'method' => 'PUT',
            'endpoint' => 'tickets/update_many.json',
            'postFields' => [ 'tickets' => $tickets ]
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
            'method' => 'GET',
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

        $collaborators = $this->client->tickets()->collaborators(array( 'id' => 12345 ));

        $this->assertLastRequestIs(
          [
            'method' => 'GET',
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

        $incidents = $this->client->tickets()->incidents(array( 'id' => 12345 ));

        $this->assertLastRequestIs(
          [
            'method' => 'GET',
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
            'method' => 'GET',
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

        $this->client->tickets()->problemAutoComplete(array( 'text' => 'foo' ));

        $this->assertLastRequestIs(
          [
            'method' => 'POST',
            'endpoint' => 'problems/autocomplete.json',
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
            'method' => 'PUT',
            'endpoint' => 'tickets/12345/mark_as_spam.json',
          ]
        );
    }

    public function testMarkManyAsSpam()
    {
        $this->mockAPIResponses([
          new Response(200, [], '')
        ]);

        $this->client->tickets()->markAsSpam([ 12345, 54321 ]);

        $this->assertLastRequestIs(
          [
            'method' => 'PUT',
            'endpoint' => 'tickets/mark_many_as_spam.json',
            'queryParams' => [ 'ids' => '12345,54321' ]
          ]
        );
    }
}
