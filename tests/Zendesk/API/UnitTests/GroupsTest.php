<?php

namespace Zendesk\API\UnitTests;

use Zendesk\API\Client;

/**
 * Groups test class
 */
class GroupsTest extends BasicTest
{

    public function setUP()
    {
        // Create Group Mock Object
        $group_mock_object = new \stdClass();
        $group_mock_object->group = new \stdClass();
        $group_mock_object->group->name = 'New Group';
        $group_mock_object->group->id = 123456;

        // Set Variables that will be used in other tests
        $this->mock = $this->getMock('Groups', array('create', 'findAll', 'find', 'update', 'delete'));
        $this->group = $group_mock_object;
    }

    public function create()
    {
        // Test for Create Method
        $this->mock->expects($this->any())->method('create')->will($this->returnValue($this->returnArgument(0)));
        $group = $this->mock->create($this->group);

        $this->assertEquals(is_object($group), true, 'Should return an object');
        $this->assertEquals(is_object($group->group), true, 'Should return an object called "group"');
        $this->assertGreaterThan(0, $group->group->id, 'Returns a non-numeric id for group');
        $this->assertEquals($group->group->name, 'New Group', 'Name of test group does not match');
    }

    public function testAll()
    {
        // Test for FindAll Method
        $this->mock->expects($this->any())->method('findAll')->will($this->returnValue($this->group));
        $groups = $this->mock->findAll();

        $this->assertEquals(is_object($groups), true, 'Should return an object');
        $this->assertEquals(is_object($groups->group), true, 'Should return an object called "group"');
        $this->assertGreaterThan(0, $groups->group->id, 'Returns a non-numeric id for groups[0]');
    }

    public function testAssignable()
    {
        // Test for FindAll Method with 'assignable' parameter
        $this->mock->expects($this->any())->method('findAll')->will($this->returnValue($this->group));
        $groups = $this->mock->findAll(array('assignable' => true));

        $this->assertEquals(is_object($groups), true, 'Should return an object');
        $this->assertEquals(is_object($groups->group), true,
            'Should return an object containing an array called "groups"');
        $this->assertGreaterThan(0, $groups->group->id, 'Returns a non-numeric id for groups[0]');
    }

    public function testFind()
    {
        // Test for Find Method
        $this->mock->expects($this->any())->method('find')->will($this->returnValue($this->group));
        $group = $this->mock->find($this->group->group->id);

        $this->assertEquals(is_object($group), true, 'Should return an object');
        $this->assertGreaterThan(0, $group->group->id, 'Returns a non-numeric id for group');
    }

    public function testUpdate()
    {
        // Test for Update Method
        $this->mock->expects($this->any())->method('update')->will($this->returnArgument(0));
        $this->group->group->name = "New Group II";
        $group = $this->mock->update($this->group);

        $this->assertEquals(is_object($group), true, 'Should return an object');
        $this->assertEquals(is_object($group->group), true, 'Should return an object called "group"');
        $this->assertGreaterThan(0, $group->group->id, 'Returns a non-numeric id for group');
        $this->assertEquals($group->group->name, 'New Group II', 'Name of test group does not match');
    }

    public function tearDown()
    {
        // Test for Delete Method
        $this->mock->expects($this->any())->method('delete')->will($this->returnValue(null));
        $group = $this->mock->delete($this->group->group->id);

        $this->assertEquals(null, $group, 'Does not return a null object');
    }
}
