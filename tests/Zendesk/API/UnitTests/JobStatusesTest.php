<?php

namespace Zendesk\API\UnitTests;

class JobStatusesTest extends BasicTest
{
    public function testFindMethodOnly()
    {
        $routes = $this->client->jobStatuses()->getRoutes();

        $this->assertArrayHasKey('find', $routes);
        $this->assertEquals(1, count($routes), 'Should contain only routes for find');
    }
}
