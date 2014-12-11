<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Search test class
 */
class SearchTest extends BasicTest {

    public function testCredentials() {
        $this->assertEquals($_ENV['SUBDOMAIN'] != '', true, 'Expecting _ENV[SUBDOMAIN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['TOKEN'] != '', true, 'Expecting _ENV[TOKEN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['USERNAME'] != '', true, 'Expecting _ENV[USERNAME] parameter; does phpunit.xml exist?');
    }

    public function testAuthToken() {
        $this->client->setAuth('token', $this->token);
        $tickets = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testSearch() {
        $results = $this->client->performSearch(array('query' => 'hello'));
        $this->assertEquals(is_object($results), true, 'Should return an object');
        $this->assertEquals(is_array($results->results), true, 'Should return an object containing an array called "results"');
        $this->assertGreaterThan(0, $results->results[0]->id, 'Returns a non-numeric id for results[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAnonymousSearch() {
        $results = $this->client->anonymousSearch(array('query' => 'hello'));
        $this->assertEquals(is_object($results), true, 'Should return an object');
        $this->assertEquals(is_array($results->results), true, 'Should return an object containing an array called "results"');
        $this->assertGreaterThan(0, $results->results[0]->id, 'Returns a non-numeric id for results[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
