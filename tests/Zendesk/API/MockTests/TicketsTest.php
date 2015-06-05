<?php

namespace Zendesk\API\MockTests;

use Zendesk\API\HttpClient;
use Zendesk\API\ResponseException;

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
            'subject' => 'The quick brown fox jumps over the lazy dog',
            'comment' => array(
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ),
            'priority' => 'normal',
            'id' => "12345"
        );

        $this->testTicket2 = array(
            'subject' => 'The second ticket',
            'comment' => array(
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ),
            'priority' => 'normal',
            'id' => '4321'
        );

        parent::setUp();
    }

    public function testAll()
    {
        $this->mockApiCall("GET",
            "tickets.json",
            array("tickets" => [$this->testTicket])
        );

        $tickets = $this->client->tickets()->findAll();
        $this->httpMock->verify();

        $this->assertEquals(is_array($tickets->tickets), true,
            'Should return an object containing an array called "tickets"');
        $this->assertEquals($this->testTicket['id'], $tickets->tickets[0]->id, 'Includes the id of the first ticket');
    }

    public function testAllSideLoadedMethod()
    {
        $this->mockApiCall(
            "GET",
            "tickets.json",
            array(
                "tickets" => [$this->testTicket],
                "groups" => [],
                "users" => []
            ),
            ["queryParams" => ["include" => "users,groups"]]
        );

        $tickets = $this->client->tickets()->sideload(array('users', 'groups'))->findAll();
        $this->httpMock->verify();

        $this->assertEquals(is_array($tickets->users), true,
            'Should return an object containing an array called "users"');
        $this->assertEquals(is_array($tickets->groups), true,
            'Should return an object containing an array called "groups"');
    }

    public function testAllSideLoadedParameter()
    {
        $this->mockApiCall(
            "GET",
            "tickets.json",
            array(
                "tickets" => [$this->testTicket],
                "groups" => [],
                "users" => []
            ),
            ["queryParams" => array('include' => implode(",", array('users', 'groups')))]
        );

        $tickets = $this->client->tickets()->findAll(array('sideload' => array('users', 'groups')));
        $this->httpMock->verify();

        $this->assertEquals(is_array($tickets->users), true,
            'Should return an object containing an array called "users"');
        $this->assertEquals(is_array($tickets->groups), true,
            'Should return an object containing an array called "groups"');
    }

    public function testFindSingle()
    {
        $this->mockApiCall(
            "GET", 'tickets/' . $this->testTicket['id'] . ".json",
            ["ticket" => $this->testTicket]
        );

        $tickets = $this->client->tickets()->find($this->testTicket['id']);
        $this->httpMock->verify();

        $this->assertEquals(is_object($tickets->ticket), true, 'Should return an object called "ticket"');
        $this->assertEquals($tickets->ticket->id, $this->testTicket['id'],
            'Should return an object with the right ticket ID');

    }

    public function testFindSingleChainPattern()
    {
        $this->mockApiCall(
            "GET",
            'tickets/' . $this->testTicket['id'] . ".json",
            ["ticket" => $this->testTicket]
        );

        $tickets = $this->client->tickets($this->testTicket['id'])->find();
        $this->httpMock->verify();

        $this->assertEquals(is_object($tickets->ticket), true, 'Should return an object called "ticket"');
        $this->assertEquals($tickets->ticket->id, $this->testTicket['id'],
            'Should return an object with the right ticket ID');
    }

    public function testFindMultiple()
    {
        $this->mockApiCall(
            "GET",
            "tickets/show_many.json",
            ["tickets" => array($this->testTicket, $this->testTicket2)],
            ["queryParams" => array('ids' => implode(",", [$this->testTicket['id'], $this->testTicket2['id']]))]
        );

        $testTicketIds = ['ids' => [$this->testTicket['id'], $this->testTicket2['id']]];
        $tickets = $this->client->tickets()->findMany($testTicketIds);
        $this->httpMock->verify();

        $this->assertEquals($tickets->tickets[0]->id, $this->testTicket['id']);
        $this->assertEquals($tickets->tickets[1]->id, $this->testTicket2['id']);
    }

    public function testUpdate()
    {
        $this->mockApiCall(
            "PUT",
            "tickets/" . $this->testTicket['id'] . ".json",
            array("ticket" => $this->testTicket),
            ["bodyParams" => ["ticket" => $this->testTicket]]
        );

        $this->client->tickets()->update($this->testTicket['id'], $this->testTicket);
        $this->httpMock->verify();
    }

    public function testDelete()
    {
        $this->mockApiCall('DELETE', 'tickets/' . $this->testTicket['id'] . '.json', array());
        $this->client->tickets()->delete($this->testTicket['id']);
    }

    public function testDeleteMultiple()
    {
        $this->mockApiCall(
            "DELETE",
            "tickets/destroy_many.json?ids=123%2C321",
            array("tickets" => []),
            ["queryParams" => array('ids' => implode(",", [123, 321]))]
        );

        $this->client->tickets()->deleteMany(array(123, 321));
        $this->httpMock->verify();
    }

    public function testCreateWithAttachment()
    {
        $this->markTestSkipped("Waiting for side chaining to support this");
        
        $this->mockApiCall("POST", "/uploads.json?filename=UK%20test%20non-alpha%20chars.png",
            array("upload" => array("token" => "asdf")), ["code" => 201]);

        $this->mockApiCall("POST", "tickets.json", array("ticket" => array("id" => "123")),
            array('code' => 201));

        $ticket = $this->client->tickets()->attach(array(
            'file' => getcwd() . '/tests/assets/UK.png',
            'name' => 'UK test non-alpha chars.png'
        ))->create($this->testTicket);
        $this->httpMock->verify();

        $contentType = $this->http->requests->first()->getHeader("Content-Type")->toArray()[0];
        $this->assertEquals($contentType, "application/binary");
    }

    public function testExport()
    {
        $this->mockApiCall("GET",
            "exports/tickets.json",
            array("results" => []),
            ["queryParams" => ["start_time" => "1332034771"]]
        );
        $tickets = $this->client->tickets()->export(array('start_time' => '1332034771'));
        $this->httpMock->verify();

        $this->assertEquals(is_object($tickets), true, 'Should return an object');
        $this->assertEquals(is_array($tickets->results), true,
            'Should return an object containing an array called "results"');
    }

    public function testUpdateManyWithQueryParams()
    {
        $ticketIds = [$this->testTicket['id'], $this->testTicket2['id']];

        $this->mockApiCall(
            "PUT",
            "tickets/update_many.json",
            array("job_status" => ["job_status" => ["id" => "bugger off"]]),
            [
                "queryParams" => ["ids" => implode(",", $ticketIds)],
                "bodyParams" => ["ticket" => ["status" => "solved"]]
            ]
        );


        $this->client->tickets()->updateMany(
            [
                "ids" => $ticketIds,
                "status" => "solved"
            ]
        );
        $this->httpMock->verify();
    }

    public function testUpdateMany()
    {
        $tickets = [$this->testTicket, $this->testTicket2];

        $this->mockApiCall(
            "PUT",
            "tickets/update_many.json",
            array("job_status" => ["job_status" => ["id" => "bugger off"]]),
            [
                "bodyParams" => ["tickets" => $tickets]
            ]
        );

        $this->client->tickets()->updateMany($tickets);
        $this->httpMock->verify();
    }

    public function tearDown()
    {
        $testTicket = $this->testTicket;
        $this->assertGreaterThan(0, $testTicket['id'], 'Cannot find a ticket id to test with. Did setUp fail?');
        parent::tearDown();
    }

}
