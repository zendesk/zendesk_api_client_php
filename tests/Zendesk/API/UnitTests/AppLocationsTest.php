<?php

namespace Zendesk\API\UnitTests;

class AppLocationsTest extends BasicTest
{
    /**
     * Test that the find and findAll methods were included
     */
    public function testRoutes()
    {
        $this->assertTrue(method_exists($this->client->appLocations(), 'find'));
        $this->assertTrue(method_exists($this->client->appLocations(), 'findAll'));
    }
}
