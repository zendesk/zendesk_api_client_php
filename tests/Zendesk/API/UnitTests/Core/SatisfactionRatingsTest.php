<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Satisfaction Ratings test class
 */
class SatisfactionRatingsTest extends BasicTest
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
                'satisfaction_ratings' => [$this->testResource0, $this->testResource1],
                'meta' => ['after_cursor' => '<after_cursor>', 'has_more' => true],

            ])),
            new Response(200, [], json_encode([
                'satisfaction_ratings' => [$this->testResource2],
                'meta' => ['has_more' => false],

            ])),
        ]);

        $iterator = $this->client->satisfactionRatings()->iterator();

        $actual = $this->iterator_to_array($iterator);
        $this->assertCount(3, $actual);
        $this->assertEquals($this->testResource0['anyField'], $actual[0]->anyField);
        $this->assertEquals($this->testResource1['anyField'], $actual[1]->anyField);
        $this->assertEquals($this->testResource2['anyField'], $actual[2]->anyField);
    }

    /**
     * Test that the correct traits were added by checking the available methods
     */
    public function testMethods()
    {
        $this->assertTrue(method_exists($this->client->satisfactionRatings(), 'create'));
        $this->assertTrue(method_exists($this->client->satisfactionRatings(), 'find'));
        $this->assertTrue(method_exists($this->client->satisfactionRatings(), 'findAll'));
    }

    /**
     * Test the create method.
     */
    public function testCreate()
    {
        $postParams = [
            'score' => 'good',
            'comment' => 'Awesome Support!',
        ];

        $ticketId = 123;

        $this->assertEndpointCalled(
            function () use ($ticketId, $postParams) {
                $this->client->tickets($ticketId)->satisfactionRatings()->create($postParams);
            },
            "tickets/{$ticketId}/satisfaction_rating.json",
            'POST'
        );
    }

    /**
     * Test the create method requires a ticket id
     */
    public function testCreateNeedsTicketId()
    {
        $postParams = [
            'score' => 'good',
            'comment' => 'Awesome Support!',
        ];

        $this->setExpectedException(
            'Zendesk\API\Exceptions\MissingParametersException',
            "Missing parameters: 'ticket_id' must be supplied for Zendesk\API\Resources\Core\SatisfactionRatings::create"
        );

        $this->client->satisfactionRatings()->create($postParams);
    }
}
