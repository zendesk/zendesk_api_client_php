<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Settings test class
 */
class SettingsTest extends BasicTest {
    
    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    public function testAll() {
        $settings = $this->client->settings()->findAll();
        $this->assertEquals(is_object($settings), true, 'Should return an object');
        $this->assertEquals(is_object($settings->settings), true, 'Should return an object called "settings"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate() {
        $settings = $this->client->settings()->update(array(
            'lotus' => array(
                'prefer_lotus' => false
            )
        ));
        $this->assertEquals(is_object($settings), true, 'Should return an object');
        $this->assertEquals(is_object($settings->settings), true, 'Should return an object called "settings"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
