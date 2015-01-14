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

    protected $ticket_id;
    
    public function setUP(){
        $testTicket = array(
            'subject' => 'Ticket Metrics test', 
            'comment' => array (
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ), 
            'priority' => 'normal'
        );
        $ticket = $this->client->tickets()->create($testTicket);
        $this->ticket_id = $ticket->ticket->id;
    }
    
    public function tearDown(){
        $this->client->ticket($this->ticket_id)->delete();
    }

    public function testAll() {
        $metrics = $this->client->tickets()->metrics()->findAll();
        $this->assertEquals(is_object($metrics), true, 'Should return an object');
        $this->assertEquals(is_array($metrics->ticket_metrics), true, 'Should return an object containing an array called "ticket_metrics"');
        $this->assertGreaterThan(0, $metrics->ticket_metrics[0]->id, 'Returns a non-numeric id for ticket_metrics[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind() {
        $metrics_id = $this->client->tickets()->metrics()->findAll()->ticket_metrics[0]->id;
        $metric = $this->client->tickets()->metric($metrics_id)->find();
        $this->assertEquals(is_object($metric), true, 'Should return an object');
        $this->assertEquals(is_object($metric->ticket_metric), true, 'Should return an object called "ticket_metric"');
        $this->assertGreaterThan(0, $metric->ticket_metric->id, 'Returns a non-numeric id for ticket_metric');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
