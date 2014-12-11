<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Settings test class
 */
class SettingsTest extends BasicTest {
    
    public function testCredentials() {
        $this->assertEquals($_ENV['SUBDOMAIN'] != '', true, 'Expecting _ENV[SUBDOMAIN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['TOKEN'] != '', true, 'Expecting _ENV[TOKEN] parameter; does phpunit.xml exist?');
        $this->assertEquals($_ENV['USERNAME'] != '', true, 'Expecting _ENV[USERNAME] parameter; does phpunit.xml exist?');
    }

    public function testAuthToken() {
        $this->client->setAuth('token', $this->token);
        $requests = $this->client->tickets()->findAll();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAll() {
        $settings = $this->client->settings()->findAll();
        $this->assertEquals(is_object($settings), true, 'Should return an object');
        $this->assertEquals(is_object($settings->settings), true, 'Should return an object called "settings"');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
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
