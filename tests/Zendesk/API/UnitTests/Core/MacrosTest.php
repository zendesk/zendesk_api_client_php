<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Macros test class
 * Class MacrosTest
 */
class MacrosTest extends BasicTest
{
    protected $testResource0;
    protected $testResource1;
    protected $testResource2;

    public function setUp()
    {
        $this->testResource0 = ['anyField'  => 'Any field 0'];
        $this->testResource1 = ['anyField'  => 'Any field 1'];
        $this->testResource2 = ['anyField'  => 'Any field 2'];
        parent::setUp();
    }

    public function testIterator()
    {
        // CBP
        $this->mockApiResponses([
            new Response(200, [], json_encode([
                'macros' => [$this->testResource0, $this->testResource1],
                'meta' => ['after_cursor' => '<after_cursor>', 'has_more' => true],

            ])),
            new Response(200, [], json_encode([
                'macros' => [$this->testResource2],
                'meta' => ['has_more' => false],

            ])),
        ]);

        $iterator = $this->client->macros()->iterator();

        $actual = $this->iterator_to_array($iterator);
        $this->assertCount(3, $actual);
        $this->assertEquals($this->testResource0['anyField'], $actual[0]->anyField);
        $this->assertEquals($this->testResource1['anyField'], $actual[1]->anyField);
        $this->assertEquals($this->testResource2['anyField'], $actual[2]->anyField);
    }

    /**
     * Test the `GET /api/v2/macros/active.json` endpoint
     * Lists active macros for the current user
     */
    public function testActive()
    {
        $this->assertEndpointCalled(function () {
            $this->client->macros()->findAllActive();
        }, 'macros/active.json');
    }

    /**
     * Test the `GET /api/v2/macros/{id}/apply.json` endpoint
     * Shows the changes to the ticket
     */
    public function testApply()
    {
        $id = 1;

        $this->assertEndpointCalled(function () use ($id) {
            $this->client->macros()->apply($id);
        }, "macros/{$id}/apply.json");
    }

    /**
     * Test the `GET /api/v2/tickets/{ticket_id}/macros/{id}/apply.json` endpoint
     * Shows the ticket after the macro changes
     */
    public function testApplyToTicket()
    {
        $id       = 1;
        $ticketId = 3;

        $this->assertEndpointCalled(function () use ($id, $ticketId) {
            $this->client->macros()->applyToTicket($id, $ticketId);
        }, "tickets/{$ticketId}/macros/{$id}/apply.json");
    }
}
