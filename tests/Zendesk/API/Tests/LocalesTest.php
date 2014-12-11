<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Locals test class
 */
class LocalsTest extends BasicTest {

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
        $locales = $this->client->locales()->findAll();
        $this->assertEquals(is_object($locales), true, 'Should return an object');
        $this->assertEquals(is_array($locales->locales), true, 'Should return an array of objects called "locales"');
        $this->assertGreaterThan(0, $locales->locales[0]->id, 'Returns a non-numeric id for locales');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAgent() {
        $locales = $this->client->locales()->agent();
        $this->assertEquals(is_object($locales), true, 'Should return an object');
        $this->assertEquals(is_array($locales->locales), true, 'Should return an array of objects called "locales"');
        $this->assertGreaterThan(0, $locales->locales[0]->id, 'Returns a non-numeric id for locales');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCurrent() {
        $locale = $this->client->locales()->current();
        $this->assertEquals(is_object($locale), true, 'Should return an object');
        $this->assertEquals(is_object($locale->locale), true, 'Should return an object called "locale"');
        $this->assertGreaterThan(0, $locale->locale->id, 'Returns a non-numeric id for locale');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $locale = $this->client->locale(1)->find();
        $this->assertEquals(is_object($locale), true, 'Should return an object');
        $this->assertEquals(is_object($locale->locale), true, 'Should return an object called "locale"');
        $this->assertGreaterThan(0, $locale->locale->id, 'Returns a non-numeric id for locale');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testDetectBest() {
        $locale = $this->client->locales()->detectBest(array('available_locales' => array('en', 'js', 'es')));
        $this->assertEquals(is_object($locale), true, 'Should return an object');
        $this->assertEquals(is_object($locale->locale), true, 'Should return an object called "locale"');
        $this->assertGreaterThan(0, $locale->locale->id, 'Returns a non-numeric id for locale');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
