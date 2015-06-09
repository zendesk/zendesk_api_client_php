<?php

namespace Zendesk\API\MockTests;


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

        $this->client->tickets()->sideload(array('users', 'groups'))->findAll();
        $this->httpMock->verify();
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

        $this->client->tickets()->findAll(array('sideload' => array('users', 'groups')));
        $this->httpMock->verify();
    }

    public function testFindSingle()
    {
        $this->mockApiCall(
            "GET", 'tickets/' . $this->testTicket['id'] . ".json",
            ["ticket" => $this->testTicket]
        );

        $this->client->tickets()->find($this->testTicket['id']);
        $this->httpMock->verify();

    }

    public function testFindSingleChainPattern()
    {
        $this->mockApiCall(
            "GET",
            'tickets/' . $this->testTicket['id'] . ".json",
            ["ticket" => $this->testTicket]
        );

        $this->client->tickets($this->testTicket['id'])->find();
        $this->httpMock->verify();
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
        $this->client->tickets()->findMany($testTicketIds);
        $this->httpMock->verify();
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

        $this->client->tickets()->attach(array(
            'file' => getcwd() . '/tests/assets/UK.png',
            'name' => 'UK test non-alpha chars.png'
        ))->create($this->testTicket);
        $this->httpMock->verify();
    }

    public function testExport()
    {
        $this->mockApiCall("GET",
            "exports/tickets.json",
            array("results" => []),
            ["queryParams" => ["start_time" => "1332034771"]]
        );
        $this->client->tickets()->export(array('start_time' => '1332034771'));
        $this->httpMock->verify();
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

    public function testRelated()
    {
        $this->mockApiCall(
            'GET',
            'tickets/12345/related.json',
            array('topic_id' => 1),
            array('statusCode' => 200)
        );

        $related = $this->client->tickets(12345)->related();

        $this->httpMock->verify();

        // Test if the method returns readable data
        $this->assertEquals(is_object($related), true, 'Should return an object');
    }

    public function testCollaborators()
    {
        $this->mockApiCall(
            'GET',
            'tickets/12345/collaborators.json',
            array('topic_id' => 1),
            array('statusCode' => 200)
        );

        $collaborators = $this->client->tickets()->collaborators(array('id' => 12345));

        $this->httpMock->verify();

        $this->assertEquals(is_object($collaborators), true, 'Should return an object');
    }

    public function testIncidents()
    {
        $this->mockApiCall(
            'GET',
            'tickets/12345/incidents.json',
            array('topic_id' => 1),
            array('statusCode' => 200)
        );

        $incidents = $this->client->tickets()->incidents(array('id' => 12345));

        $this->httpMock->verify();

        $this->assertEquals(is_object($incidents), true, 'Should return an object');
    }

    public function testProblems()
    {
        $this->mockApiCall(
            'GET',
            'problems.json',
            array('tickets' => []),
            array('statusCode' => 200)
        );

        $problems = $this->client->tickets()->problems();

        $this->httpMock->verify();

        $this->assertEquals(is_object($problems), true, 'Should return an object');
    }

    public function testProblemAutoComplete()
    {
        $this->mockApiCall(
            'POST',
            'problems/autocomplete.json',
            array('tickets' => []),
            array(
                'statusCode' => 200,
                'bodyParams' => ['text' => 'foo']
            )
        );

        $this->client->tickets()->problemAutoComplete(array('text' => 'foo'));

        $this->httpMock->verify();
    }

    public function testMarkAsSpam()
    {
        $this->mockApiCall(
            'PUT',
            'tickets/12345/mark_as_spam.json',
            [],
            ['statusCode' => 200]
        );

        $this->client->tickets(12345)->markAsSpam();
        $this->httpMock->verify();
    }

    public function testMarkManyAsSpam()
    {
        $this->mockApiCall(
            'PUT',
            'tickets/mark_many_as_spam.json',
            [],
            [
                'statusCode' => 200,
                'queryParams' => ['ids' => '12345,54321']
            ]
        );

        $this->client->tickets()->markAsSpam([12345, 54321]);
        $this->httpMock->verify();
    }
}
