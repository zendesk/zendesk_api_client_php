<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Ticket Metrics test class
 */
class TicketMetricsTest extends \PHPUnit_Framework_TestCase {

    private $client;
    private $subdomain;
    private $username;
    private $password;
    private $token;
    private $oAuthToken;

    public function __construct() {
        $this->subdomain = $GLOBALS['SUBDOMAIN'];
        $this->username = $GLOBALS['USERNAME'];
        $this->password = $GLOBALS['PASSWORD'];
        $this->token = $GLOBALS['TOKEN'];
        $this->oAuthToken = $GLOBALS['OAUTH_TOKEN'];
        $this->client = new Client($this->subdomain, $this->username);
        $this->client->setAuth('token', $this->token);
    }

    public function testCredentials() {
        $this->assertEquals($GLOBALS['SUBDOMAIN'] != '', true, 'Expecting GLOBALS[SUBDOMAIN] parameter; does phpunit.xml exist?');
        $this->assertEquals($GLOBALS['TOKEN'] != '', true, 'Expecting GLOBALS[TOKEN] parameter; does phpunit.xml exist?');
        $this->assertEquals($GLOBALS['USERNAME'] != '', true, 'Expecting GLOBALS[USERNAME] parameter; does phpunit.xml exist?');
    }

    public function testAuthToken() {
        $this->client->setAuth('token', $this->token);
        $tickets = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
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
