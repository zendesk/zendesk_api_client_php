<?php

namespace Zendesk\API\LiveTests;

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
        $this->mockApiCall("GET", "/tickets.json", array("tickets" => [$this->testTicket]));

        $tickets = $this->client->tickets()->findAll();

        $this->assertEquals(is_array($tickets->tickets), true,
            'Should return an object containing an array called "tickets"');
        $this->assertEquals($this->testTicket['id'], $tickets->tickets[0]->id, 'Includes the id of the first ticket');
    }

    public function testAllSideLoadedMethod()
    {
        $this->mockApiCall("GET", "/tickets.json?include=users%2Cgroups",
            array(
                "tickets" => [$this->testTicket],
                "groups" => [],
                "users" => []
            ));

        $tickets = $this->client->tickets()->sideload(array('users', 'groups'))->findAll();

        $this->assertEquals(is_array($tickets->users), true,
            'Should return an object containing an array called "users"');
        $this->assertEquals(is_array($tickets->groups), true,
            'Should return an object containing an array called "groups"');
    }

    public function testAllSideLoadedParameter()
    {
        $this->mockApiCall("GET", "/tickets.json?include=users%2Cgroups",
            array(
                "tickets" => [$this->testTicket],
                "groups" => [],
                "users" => []
            ));

        $tickets = $this->client->tickets()->findAll(array('sideload' => array('users', 'groups')));
        $this->assertEquals(is_array($tickets->users), true,
            'Should return an object containing an array called "users"');
        $this->assertEquals(is_array($tickets->groups), true,
            'Should return an object containing an array called "groups"');
    }

    public function testFindSingle()
    {
        $this->mockApiCall("GET", '/tickets/' . $this->testTicket['id'] . ".json",
            array("ticket" => $this->testTicket));

        $tickets = $this->client->tickets()->find($this->testTicket['id']);

        $this->assertEquals(is_object($tickets->ticket), true, 'Should return an object called "ticket"');
        $this->assertEquals($tickets->ticket->id, $this->testTicket['id'],
            'Should return an object with the right ticket ID');
    }

    public function testFindSingleChainPattern()
    {
        $this->mockApiCall("GET", '/tickets/' . $this->testTicket['id'] . ".json",
          array("ticket" => $this->testTicket));

        $tickets = $this->client->tickets($this->testTicket['id'])->find();

        $this->assertEquals(is_object($tickets->ticket), true, 'Should return an object called "ticket"');
        $this->assertEquals($tickets->ticket->id, $this->testTicket['id'],
          'Should return an object with the right ticket ID');
    }

    public function testFindMultiple()
    {
        $this->mockApiCall("GET", "/tickets/show_many.json?ids={$this->testTicket['id']}%2C{$this->testTicket2['id']}",
            array("tickets" => array($this->testTicket, $this->testTicket2)));

        $testTicketIds = ['ids' => [$this->testTicket['id'], $this->testTicket2['id']]];
        $tickets = $this->client->tickets()->findMany($testTicketIds);

        $this->assertEquals($tickets->tickets[0]->id, $this->testTicket['id']);
        $this->assertEquals($tickets->tickets[1]->id, $this->testTicket2['id']);
    }

    public function testUpdate()
    {
        $this->mockApiCall("PUT", "tickets/" . $this->testTicket['id'] . ".json", array("ticket" => $this->testTicket),
            ["postFields" => ["ticket" => $this->testTicket]]);

        $this->client->tickets()->update($this->testTicket['id'], $this->testTicket);
        $this->httpMock->verify();
    }

    public function testDelete()
    {
        $this->mockApiCall('DELETE', '/tickets/' . $this->testTicket['id']. '.json', array());
        $this->client->tickets()->delete($this->testTicket['id']);
    }

    public function testDeleteMultiple()
    {
        $this->mockApiCall("DELETE", "/tickets/destroy_many.json?ids=123%2C321", array("tickets" => []));

        $this->client->tickets()->deleteMany(array(123, 321));
    }

    public function testCreateWithAttachment()
    {
        $this->mockApiCall("POST", "/uploads.json?filename=UK%20test%20non-alpha%20chars.png",
            array("upload" => array("token" => "asdf")), ["code" => 201]);

        $this->mockApiCall("POST", "/tickets.json", array("ticket" => array("id" => "123")),
            array('code' => 201));

        $ticket = $this->client->tickets()->attach(array(
            'file' => getcwd() . '/tests/assets/UK.png',
            'name' => 'UK test non-alpha chars.png'
        ))->create($this->testTicket);

        $contentType = $this->http->requests->first()->getHeader("Content-Type")->toArray()[0];
        $this->assertEquals($contentType, "application/binary");
    }

    public function testExport()
    {
        $this->mockApiCall("GET", "/exports/tickets.json?start_time=1332034771", array("results" => []));
        $tickets = $this->client->tickets()->export(array('start_time' => '1332034771'));
        $this->assertEquals(is_object($tickets), true, 'Should return an object');
        $this->assertEquals(is_array($tickets->results), true,
            'Should return an object containing an array called "results"');
    }

    public function testCreateFromTweet()
    {
        $this->markTestSkipped(
            'Skipped for now because it requires a new (unique) twitter id for each test'
        );
        $twitter_id = $this->client->twitter()->handles()->monitored_twitter_handles[0]->id;
        $params = array(
            'monitored_twitter_handle_id' => $twitter_id,
            'twitter_status_message_id' => '419191717649473536'
        );
        $ticket = $this->client->tickets()->createFromTweet($params);
        $this->assertEquals(is_object($ticket), true, 'Should return an object');
        $this->assertEquals(is_object($ticket->ticket), true, 'Should return an object called "ticket"');
        $this->assertGreaterThan(0, $ticket->ticket->id, 'Returns a non-numeric id for ticket');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Create does not return HTTP code 201');
        $this->client->tickets()->delete(array('id' => $ticket->ticket->id));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Delete does not return HTTP code 200');
    }


    public function testUpdateMany()
    {
        $this->mockApiCall(
            "PUT",
            "/tickets/update_many.json",
            array("job_status" => ["job_status" => ["id" => "bugger off"]])
        );

        $this->client->tickets()->updateMany(
            [$this->testTicket['id'], $this->testTicket2['id']]
        );
    }

    public function testUpdateManyWithQueryParams()
    {
        $ticketIds = [$this->testTicket['id'], $this->testTicket2['id']];

        $this->mockApiCall(
            "PUT",
            "/tickets/update_many.json?ids=" . urlencode(
                implode(",", $ticketIds)
            ),
            array("job_status" => ["job_status" => ["id" => "bugger off"]])
        );

        $this->client->tickets()->updateMany(
            ['ids' => $ticketIds, "status" => "closed"]
        );
    }

    public function tearDown()
    {
        $testTicket = $this->testTicket;
        $this->assertGreaterThan(0, $testTicket['id'], 'Cannot find a ticket id to test with. Did setUp fail?');
        parent::tearDown();
    }

}
