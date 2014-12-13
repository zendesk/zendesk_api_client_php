<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Ticket Metrics test class
 */
class TicketMetricsTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    /**
     * @depends testAuthToken
     */
    public function testAll() {
        $metrics = $this->client->tickets()->metrics()->findAll();
        $this->assertEquals(is_object($metrics), true, 'Should return an object');
        $this->assertEquals(is_array($metrics->ticket_metrics), true, 'Should return an object containing an array called "ticket_metrics"');
        $this->assertGreaterThan(0, $metrics->ticket_metrics[0]->id, 'Returns a non-numeric id for ticket_metrics[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $metric = $this->client->tickets()->metric(631964852)->find();
        $this->assertEquals(is_object($metric), true, 'Should return an object');
        $this->assertEquals(is_object($metric->ticket_metric), true, 'Should return an object called "ticket_metric"');
        $this->assertGreaterThan(0, $metric->ticket_metric->id, 'Returns a non-numeric id for ticket_metric');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
