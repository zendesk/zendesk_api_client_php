<?php

namespace Zendesk\API\UnitTests;

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
}
