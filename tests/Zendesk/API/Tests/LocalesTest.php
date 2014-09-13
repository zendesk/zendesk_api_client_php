<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Locals test class
 */
class LocalsTest extends \PHPUnit_Framework_TestCase {

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
