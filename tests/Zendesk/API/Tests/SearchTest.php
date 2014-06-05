<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Search test class
 */
class SearchTest extends \PHPUnit_Framework_TestCase {

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
