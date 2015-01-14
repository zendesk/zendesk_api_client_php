<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Locals test class
 */
class LocalsTest extends BasicTest {

    public function testCredentials() {
        parent::credentialsTest();
    }

    public function testAuthToken() {
        parent::authTokenTest();
    }

    public function testAll() {
        $locales = $this->client->locales()->findAll();
        $this->assertEquals(is_object($locales), true, 'Should return an object');
        $this->assertEquals(is_array($locales->locales), true, 'Should return an array of objects called "locales"');
        $this->assertGreaterThan(0, $locales->locales[0]->id, 'Returns a non-numeric id for locales');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testAgent() {
        $locales = $this->client->locales()->agent();
        $this->assertEquals(is_object($locales), true, 'Should return an object');
        $this->assertEquals(is_array($locales->locales), true, 'Should return an array of objects called "locales"');
        $this->assertGreaterThan(0, $locales->locales[0]->id, 'Returns a non-numeric id for locales');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testCurrent() {
        $locale = $this->client->locales()->current();
        $this->assertEquals(is_object($locale), true, 'Should return an object');
        $this->assertEquals(is_object($locale->locale), true, 'Should return an object called "locale"');
        $this->assertGreaterThan(0, $locale->locale->id, 'Returns a non-numeric id for locale');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind() {
        $locale = $this->client->locale(1)->find();
        $this->assertEquals(is_object($locale), true, 'Should return an object');
        $this->assertEquals(is_object($locale->locale), true, 'Should return an object called "locale"');
        $this->assertGreaterThan(0, $locale->locale->id, 'Returns a non-numeric id for locale');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testDetectBest() {
        $locale = $this->client->locales()->detectBest(array('available_locales' => array('en', 'js', 'es')));
        $this->assertEquals(is_object($locale), true, 'Should return an object');
        $this->assertEquals(is_object($locale->locale), true, 'Should return an object called "locale"');
        $this->assertGreaterThan(0, $locale->locale->id, 'Returns a non-numeric id for locale');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
