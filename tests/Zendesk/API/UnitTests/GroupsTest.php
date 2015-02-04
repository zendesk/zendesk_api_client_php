<?php

namespace Zendesk\API\UnitTests;

use Zendesk\API\Client;

/**
 * Groups test class
 */
class GroupsTest extends BasicTest {

    public function testCredentials() {
        //parent::credentialsTest();
    }

    public function testAuthToken() {
        //parent::authTokenTest();
    }

    protected $id;
    public function setUP() {
	    // Create Mock Object
	    $groups_mock_object = new \stdClass();
	    $groups_mock_object->group = new \stdClass();
	    $groups_mock_object->group->name = 'New Group';
	    $groups_mock_object->group->id = 123456;
	    
        $this->assertEquals(is_object($groups_mock_object), true, 'Should return an object');
        $this->assertEquals(is_object($groups_mock_object->group), true, 'Should return an object called "group"');
        $this->assertGreaterThan(0, $groups_mock_object->group->id, 'Returns a non-numeric id for group');
        $this->assertEquals($groups_mock_object->group->name, 'New Group', 'Name of test group does not match');
        $this->id = $groups_mock_object->group->id;
        $this->groups = $groups_mock_object;
    }

    public function testAll() {
		$mock = $this->getMock('Groups', array('findAll'));
    	$mock->expects($this->any())->method('findAll')->will($this->returnValue($this->groups));
	    	 
        $groups = $mock->findAll();
        $this->assertEquals(is_object($groups), true, 'Should return an object');
        $this->assertEquals(is_object($groups->group), true, 'Should return an object called "group"');
        $this->assertGreaterThan(0, $groups->group->id, 'Returns a non-numeric id for groups[0]');
    }

    public function testAssignable() {
        $mock = $this->getMock('Groups', array('findAll'));
    	$mock->expects($this->any())->method('findAll')->will($this->returnValue($this->groups));
        
        $groups = $mock->findAll(array('assignable' => true));
        $this->assertEquals(is_object($groups), true, 'Should return an object');
        $this->assertEquals(is_object($groups->group), true, 'Should return an object containing an array called "groups"');
        $this->assertGreaterThan(0, $groups->group->id, 'Returns a non-numeric id for groups[0]');
    }

    public function testFind() {
        $mock = $this->getMock('Groups', array('find'));
    	$mock->expects($this->any())->method('find')->will($this->returnValue($this->groups));

        $group = $mock->find($this->id);
        $this->assertEquals(is_object($group), true, 'Should return an object');
        $this->assertGreaterThan(0, $group->group->id, 'Returns a non-numeric id for group');
    }

    public function testUpdate() {
	    $mock = $this->getMock('Groups', array('update'));
    	$mock->expects($this->any())->method('update')->will($this->returnArgument(0));
	    
	    $this->groups->group->name = "New Group II";
	    
        $group = $mock->update($this->groups);
        $this->assertEquals(is_object($group), true, 'Should return an object');
        $this->assertEquals(is_object($group->group), true, 'Should return an object called "group"');
        $this->assertGreaterThan(0, $group->group->id, 'Returns a non-numeric id for group');
        $this->assertEquals($group->group->name, 'New Group II', 'Name of test group does not match');
    }

    public function tearDown() {
	    $mock = $this->getMock('Groups', array('delete'));
    	$mock->expects($this->any())->method('delete')->will($this->returnValue(null));
    	
        $this->assertGreaterThan(0, $this->id, 'Cannot find a group id to test with. Did setUP fail?');
        $group = $mock->delete();
        $this->assertEquals(null, $group, 'Does not return a null object');
    }

}
