<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Satisfaction Ratings test class
 */
class SatisfactionRatingsTest extends BasicTest
{
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
     *
     * @expectedException Zendesk\API\Exceptions\MissingParametersException
     * @expectedExceptionMessage Missing parameters: 'ticket_id' must be supplied for Zendesk\API\Resources\Core\SatisfactionRatings::create
     */
    public function testCreateNeedsTicketId()
    {
        $postParams = [
            'score' => 'good',
            'comment' => 'Awesome Support!',
        ];

        $this->client->satisfactionRatings()->create($postParams);
    }
}
