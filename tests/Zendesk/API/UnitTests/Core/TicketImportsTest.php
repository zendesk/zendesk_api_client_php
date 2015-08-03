<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Ticket Imports test class
 */
class TicketImportTest extends BasicTest
{
    /**
     * Test that the routes for importing were set correctly
     */
    public function testRoutes()
    {
        $routes = $this->client->ticketImports()->getRoutes();

        $this->assertEquals('imports/tickets.json', $routes['create']);
        $this->assertEquals('imports/tickets/create_many.json', $routes['createMany']);
        $this->assertEquals(2, count($routes), 'Should only have routes for create and createMany');
    }

    /**
     * Test that the trait methods exists
     */
    public function testTraitMethods()
    {
        $this->assertTrue(method_exists($this->client->ticketImports(), 'create'));
        $this->assertTrue(method_exists($this->client->ticketImports(), 'createMany'));
    }
}
