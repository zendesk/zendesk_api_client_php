<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Twitter test class
 */
class TwitterTest extends \PHPUnit_Framework_TestCase {

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

    public function testAuthToken() {
        $tickets = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testGetHandles() {
        $handles = $this->client->twitter()->handles();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $this->assertEquals(is_object($handles), true, 'Should return an object');
        $this->assertEquals(is_array($handles->monitored_twitter_handles), true, 'Should return an array called "monitored_twitter_handles"');
    }

    /**
     * @depends testAuthToken
     */
    public function testGetHandleById() {
        $handles = $this->client->twitter()->handleById(array(
            'id' => 20032352 // don't delete
        ));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $this->assertEquals(is_object($handles), true, 'Should return an object');
        $this->assertEquals(is_object($handles->monitored_twitter_handle), true, 'Should return an object called "monitored_twitter_handles"');
    }

}

?>
