<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Targets test class
 */
class TargetsTest extends \PHPUnit_Framework_TestCase {

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
        $target = $this->client->targets()->create(array(
            'type' => 'email_target',
            'title' => 'Test Email Target',
            'email' => 'hello@example.com',
            'subject' => 'Test Target'
        ));
        $this->assertEquals(is_object($target), true, 'Should return an object');
        $this->assertEquals(is_object($target->target), true, 'Should return an object called "target"');
        $this->assertGreaterThan(0, $target->target->id, 'Returns a non-numeric id for target');
        $this->assertEquals($target->target->title, 'Test Email Target', 'Title of test target does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $target->target->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testAll($stack) {
        $targets = $this->client->targets()->findAll();
        $this->assertEquals(is_object($targets), true, 'Should return an object');
        $this->assertEquals(is_array($targets->targets), true, 'Should return an object containing an array called "targets"');
        $this->assertGreaterThan(0, $targets->targets[0]->id, 'Returns a non-numeric id for targets[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testFind($stack) {
        $id = array_pop($stack);
        $target = $this->client->target($id)->find();
        $this->assertEquals(is_object($target), true, 'Should return an object');
        $this->assertGreaterThan(0, $target->target->id, 'Returns a non-numeric id for target');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $target = $this->client->target($id)->update(array(
            'email' => 'roger@example.com'
        ));
        $this->assertEquals(is_object($target), true, 'Should return an object');
        $this->assertEquals(is_object($target->target), true, 'Should return an object called "target"');
        $this->assertGreaterThan(0, $target->target->id, 'Returns a non-numeric id for target');
        $this->assertEquals($target->target->email, 'roger@example.com', 'Email of test target does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a target id to test with. Did testCreate fail?');
        $result = $this->client->target($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
