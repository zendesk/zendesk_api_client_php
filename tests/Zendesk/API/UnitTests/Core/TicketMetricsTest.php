<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Tags test class
 */
class TicketMetricsTest extends BasicTest
{
    /**
     * Test that the Tags resource class actually creates the correct routes:
     *
     * tickets/{id}/metrics.json
     * ticket_metrics.json
     * ticket_metrics/{id}.json
     */
    public function testGetRoute()
    {
        $route = $this->client->tickets(12345)->metrics()->getRoute('find', ['id' => 12345]);
        $this->assertEquals('tickets/12345/metrics.json', $route);

        $route = $this->client->tickets()->metrics(12345)->getRoute('find', ['id' => 12345]);
        $this->assertEquals('ticket_metrics/12345.json', $route);

        $route = $this->client->tickets()->metrics()->getRoute('findAll');
        $this->assertEquals('ticket_metrics.json', $route);

        $route = $this->client->tickets(12345)->metrics()->getRoute('findAll');
        $this->assertEquals('tickets/12345/metrics.json', $route);
    }
}
