<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Class AppLocationsTest
 * https://developer.zendesk.com/rest_api/docs/core/app_locations
 */
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

    /**
     * Test if the methods can be called via apps()
     */
    public function testMethodsCallable()
    {
        $this->assertEndpointCalled(function () {
            $this->client->apps()->locations()->findAll(['per_page' => 20]);
        }, 'apps/locations.json', 'GET', ['queryParams' => ['per_page' => 20]]);
    }
}
