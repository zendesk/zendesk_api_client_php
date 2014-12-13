<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * ActivityStream test class
 */
class ActivityStreamTest extends BasicTest {

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
        $activities = $this->client->activities()->findAll();
        $this->assertEquals(is_object($activities), true, 'Should return an object');
        $this->assertEquals(is_array($activities->activities), true, 'Should return an array of objects called "activities"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $activity = $this->client->activity(534322401)->find();
        $this->assertEquals(is_object($activity), true, 'Should return an object');
        $this->assertEquals(is_object($activity->activity), true, 'Should return an objects called "activity"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
