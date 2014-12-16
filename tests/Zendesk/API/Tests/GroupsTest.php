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

    protected $id;
    public function setUP() {
        $group = $this->client->groups()->create(array(
            'name' => 'New Group'
        ));
        $this->assertEquals(is_object($group), true, 'Should return an object');
        $this->assertEquals(is_object($group->group), true, 'Should return an object called "group"');
        $this->assertGreaterThan(0, $group->group->id, 'Returns a non-numeric id for group');
        $this->assertEquals($group->group->name, 'New Group', 'Name of test group does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $group->group->id;
    }

    public function testAll() {
        $groups = $this->client->groups()->findAll();
        $this->assertEquals(is_object($groups), true, 'Should return an object');
        $this->assertEquals(is_array($groups->groups), true, 'Should return an object containing an array called "groups"');
        $this->assertGreaterThan(0, $groups->groups[0]->id, 'Returns a non-numeric id for groups[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testAssignable() {
        $groups = $this->client->groups()->findAll(array('assignable' => true));
        $this->assertEquals(is_object($groups), true, 'Should return an object');
        $this->assertEquals(is_array($groups->groups), true, 'Should return an object containing an array called "groups"');
        $this->assertGreaterThan(0, $groups->groups[0]->id, 'Returns a non-numeric id for groups[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind() {
        $group = $this->client->group($this->id)->find();
        $this->assertEquals(is_object($group), true, 'Should return an object');
        $this->assertGreaterThan(0, $group->group->id, 'Returns a non-numeric id for group');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate() {
        $group = $this->client->group($this->id)->update(array(
            'name' => 'New Group II'
        ));
        $this->assertEquals(is_object($group), true, 'Should return an object');
        $this->assertEquals(is_object($group->group), true, 'Should return an object called "group"');
        $this->assertGreaterThan(0, $group->group->id, 'Returns a non-numeric id for group');
        $this->assertEquals($group->group->name, 'New Group II', 'Name of test group does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown() {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a group id to test with. Did setUP fail?');
        $view = $this->client->group($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}

?>
