<?php

namespace Zendesk\API\UnitTests;

/**
 * Activities test class
 */
class ActivitiesTest extends BasicTest
{
    public function testRoutes()
    {
        $routes = $this->client->activities()->getRoutes();

        $this->assertArrayHasKey('find', $routes);
        $this->assertArrayHasKey('findAll', $routes);

        $this->assertEquals(2, count($routes), 'Should contain only find and findAll routes.');
    }
}
