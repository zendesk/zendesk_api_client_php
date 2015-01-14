<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Search test class
 */
class SearchTest extends BasicTest {

    protected $ticket_id;

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    /*
     * Needs an existed ticket with the specified query keyword to test all Search functions
     */
    public function testSearch() {
        $results = $this->client->search(array('query' => 'hello'));
        $this->assertEquals(is_object($results), true, 'Should return an object');
        $this->assertEquals(is_array($results->results), true, 'Should return an object containing an array called "results"');
        $this->assertGreaterThan(0, $results->results[0]->id, 'Returns a non-numeric id for results[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testAnonymousSearch() {
        $results = $this->client->anonymousSearch(array('query' => 'hello'));
        $this->assertEquals(is_object($results), true, 'Should return an object');
        $this->assertEquals(is_array($results->results), true, 'Should return an object containing an array called "results"');
        $this->assertGreaterThan(0, $results->results[0]->id, 'Returns a non-numeric id for results[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }
}

?>
