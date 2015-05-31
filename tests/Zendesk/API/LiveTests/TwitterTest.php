<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Twitter test class
 */
class TwitterTest extends BasicTest
{

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    public function testGetHandles()
    {
        $handles = $this->client->twitter()->handles();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $this->assertEquals(is_object($handles), true, 'Should return an object');
        $this->assertEquals(is_array($handles->monitored_twitter_handles), true,
            'Should return an array called "monitored_twitter_handles"');
    }

    public function testGetHandleById()
    {
        $id = $this->client->twitter()->handles()->monitored_twitter_handles[0]->id;
        $handles = $this->client->twitter()->handleById(array(
            'id' => $id
        ));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $this->assertEquals(is_object($handles), true, 'Should return an object');
        $this->assertEquals(is_object($handles->monitored_twitter_handle), true,
            'Should return an object called "monitored_twitter_handles"');
    }

}
