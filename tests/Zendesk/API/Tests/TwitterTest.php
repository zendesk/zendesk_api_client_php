<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Twitter test class
 */
class TwitterTest extends BasicTest {

    public function testAuthToken() {
        parent::authTokenTest();
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
