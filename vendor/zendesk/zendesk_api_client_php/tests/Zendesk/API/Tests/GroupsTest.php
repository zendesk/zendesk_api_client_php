<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Groups test class
 */
class GroupsTest extends \PHPUnit_Framework_TestCase {

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
        $groups = $this->client->groups()->findAll();
        $this->assertEquals(is_object($groups), true, 'Should return an object');
        $this->assertEquals(is_array($groups->groups), true, 'Should return an object containing an array called "groups"');
        $this->assertGreaterThan(0, $groups->groups[0]->id, 'Returns a non-numeric id for groups[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testAssignable() {
        $groups = $this->client->groups()->findAll(array('assignable' => true));
        $this->assertEquals(is_object($groups), true, 'Should return an object');
        $this->assertEquals(is_array($groups->groups), true, 'Should return an object containing an array called "groups"');
        $this->assertGreaterThan(0, $groups->groups[0]->id, 'Returns a non-numeric id for groups[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testFind() {
        $group = $this->client->group(21526762)->find(); // don't delete group #21526762
        $this->assertEquals(is_object($group), true, 'Should return an object');
        $this->assertGreaterThan(0, $group->group->id, 'Returns a non-numeric id for group');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    /**
     * @depends testAuthToken
     */
    public function testCreate() {
        $group = $this->client->groups()->create(array(
            'name' => 'New Group'
        ));
        $this->assertEquals(is_object($group), true, 'Should return an object');
        $this->assertEquals(is_object($group->group), true, 'Should return an object called "group"');
        $this->assertGreaterThan(0, $group->group->id, 'Returns a non-numeric id for group');
        $this->assertEquals($group->group->name, 'New Group', 'Name of test group does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $group->group->id;
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $stack) {
        $id = array_pop($stack);
        $group = $this->client->group($id)->update(array(
            'name' => 'New Group II'
        ));
        $this->assertEquals(is_object($group), true, 'Should return an object');
        $this->assertEquals(is_object($group->group), true, 'Should return an object called "group"');
        $this->assertGreaterThan(0, $group->group->id, 'Returns a non-numeric id for group');
        $this->assertEquals($group->group->name, 'New Group II', 'Name of test group does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $stack = array($id);
        return $stack;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(array $stack) {
        $id = array_pop($stack);
        $this->assertGreaterThan(0, $id, 'Cannot find a group id to test with. Did testCreate fail?');
        $view = $this->client->group($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
