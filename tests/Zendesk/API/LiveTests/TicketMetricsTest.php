<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\HttpClient;

/**
 * Ticket Metrics test class
 */
class TicketMetricsTest extends BasicTest
{
    protected $ticket_id = 12345;

    public function testFindAllWithChaining()
    {
        $this->mockApiCall('GET', "/tickets/{$this->ticket_id}/metrics.json",
            array(
                'ticket_metrics' => array(
                    array('id' => 1)
                )
            )
        );
        $metrics = $this->client->tickets($this->ticket_id)->metrics()->findAll();
        $this->assertEquals(is_object($metrics), true, 'Should return an object');
        $this->assertEquals(is_array($metrics->ticket_metrics), true,
            'Should return an object containing an array called "ticket_metrics"');
        $this->assertGreaterThan(0, $metrics->ticket_metrics[0]->id, 'Returns a non-numeric id for ticket_metrics[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFindWithChaining()
    {
        $this->mockApiCall('GET', "/tickets/{$this->ticket_id}/metrics.json",
            array(
                'ticket_metrics' => array(
                    array('id' => 1)
                )
            )
        );
        $metrics_id = $this->client->tickets($this->ticket_id)->metrics()->findAll()->ticket_metrics[0]->id;

        $this->mockApiCall('GET', "/ticket_metrics/$metrics_id.json",
            array(
                'ticket_metric' => array(
                    'id' => 1
                )
            )
        );
        $metric = $this->client->tickets()->metric($metrics_id)->find();

        $this->assertEquals(is_object($metric), true, 'Should return an object');
        $this->assertEquals(is_object($metric->ticket_metric), true, 'Should return an object called "ticket_metric"');
        $this->assertGreaterThan(0, $metric->ticket_metric->id, 'Returns a non-numeric id for ticket_metric');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}
