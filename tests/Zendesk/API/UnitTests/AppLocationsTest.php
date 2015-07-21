<?php

namespace Zendesk\API\UnitTests;

class AppLocationsTest extends BasicTest
{
    /**
     * Test that the find and findAll methods were included
     */
    public function testRoutes()
    {
        $this->assertTrue(method_exists($this->client->apps()->locations(), 'find'));
        $this->assertTrue(method_exists($this->client->apps()->locations(), 'findAll'));
    }

    public function testMethodsCallable()
    {
        $this->assertEndpointCalled(function () {
            $this->client->apps()->locations()->findAll(['per_page' => 20]);
        }, 'apps/locations.json', 'GET', ['queryParams' => ['per_page' => 20]]);
    }
}
