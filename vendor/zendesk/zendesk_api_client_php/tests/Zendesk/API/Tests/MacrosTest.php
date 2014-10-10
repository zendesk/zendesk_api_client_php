<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Macros test class
 */
class MacrosTest extends \PHPUnit_Framework_TestCase {

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
    public function testCreate() {
        $macro = $this->client->macros()->create(array(
            'title' => 'Roger Wilco',
            'actions' => array(
                array(
                    'field' => 'status',
                    'value' => 'solved'
                )
            )
        ));
        $this->assertEquals(is_object($macro), true, 'Should return an object');
        $this->assertEquals(is_object($macro->macro), true, 'Should return an object called "macro"');
        $this->assertGreaterThan(0, $macro->macro->id, 'Returns a non-numeric id for macro');
        $this->assertEquals($macro->macro->title, 'Roger Wilco', 'Name of test macro does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $macro->macro->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testAll($stack) {
        $macros = $this->client->macros()->findAll();
        $this->assertEquals(is_object($macros), true, 'Should return an object');
        $this->assertEquals(is_array($macros->macros), true, 'Should return an object containing an array called "macros"');
        $this->assertGreaterThan(0, $macros->macros[0]->id, 'Returns a non-numeric id for macros[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testActive($stack) {
        $macros = $this->client->macros()->findAll(array('active' => true));
        $this->assertEquals(is_object($macros), true, 'Should return an object');
        $this->assertEquals(is_array($macros->macros), true, 'Should return an object containing an array called "macros"');
        $this->assertGreaterThan(0, $macros->macros[0]->id, 'Returns a non-numeric id for macros[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testFind($stack) {
        $id = array_pop($stack);
        $macro = $this->client->macros($id)->find();
        $this->assertEquals(is_object($macro), true, 'Should return an object');
        $this->assertGreaterThan(0, $macro->macro->id, 'Returns a non-numeric id for macro');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $macro = $this->client->macro($id)->update(array(
            'title' => 'Roger Wilco II'
        ));
        $this->assertEquals(is_object($macro), true, 'Should return an object');
        $this->assertEquals(is_object($macro->macro), true, 'Should return an object called "macro"');
        $this->assertGreaterThan(0, $macro->macro->id, 'Returns a non-numeric id for macro');
        $this->assertEquals($macro->macro->title, 'Roger Wilco II', 'Name of test macro does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a macro id to test with. Did testCreate fail?');
        $topic = $this->client->macro($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
