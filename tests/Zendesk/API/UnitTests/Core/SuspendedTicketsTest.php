<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Class SuspendedTicketsTest
 */
class SuspendedTicketsTest extends BasicTest
{
    /**
     *
     */
    public function testRoutes()
    {
        $this->assertTrue(method_exists($this->client->suspendedTickets(), 'delete'));
        $this->assertTrue(method_exists($this->client->suspendedTickets(), 'find'));
        $this->assertTrue(method_exists($this->client->suspendedTickets(), 'findAll'));
        $this->assertTrue(method_exists($this->client->suspendedTickets(), 'deleteMany'));
    }

    public function testRecover()
    {
        $resourceId = 233;
        $this->assertEndpointCalled(
            function () use ($resourceId) {
                $this->client->suspendedTickets($resourceId)->recover();
            },
            "suspended_tickets/{$resourceId}/recover.json",
            'PUT'
        );
    }

    public function testRecoverMany()
    {
        $resourceIds = [233, 232];
        $this->assertEndpointCalled(
            function () use ($resourceIds) {
                $this->client->suspendedTickets()->recoverMany($resourceIds);
            },
            "suspended_tickets/recover_many.json",
            'PUT',
            ['queryParams' => ['ids' => implode(',', $resourceIds)]]
        );
    }
}
