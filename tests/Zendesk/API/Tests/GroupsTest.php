<?php

namespace Zendesk\API\Tests;

use Zendesk\API\Client;

/**
 * Groups test class
 */
class GroupsTest extends BasicTest {

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
