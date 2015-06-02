<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * GroupMemberships test class
 */
class GroupMembershipsTest extends BasicTest
{

    public function setUp()
    {

        // Create Group Membership Mock Object
        $membership_mock_object = new \stdClass();
        $membership_mock_object->user_id = 123456;
        $membership_mock_object->group_id = 123456;
        $membership_mock_object->id = 123456;

        // Create Group Membership Mock Object for Find/Create/MakeDefault Method
        $find_membership_mock_object = new \stdClass();
        $find_membership_mock_object->group_membership = $membership_mock_object;

        // Create Group Memberships Mock Object
        $memberships_mock_object = new \stdClass();
        $memberships_mock_object->group_memberships = Array($membership_mock_object);

        // Set Variables that will be used in tests
        $this->mock = $this->getMock('GroupMemberships', array('findAll', 'find', 'create', 'delete', 'makeDefault'));
        $this->group_memberships = $memberships_mock_object;
        $this->group_membership = $membership_mock_object;
        $this->find_membership = $find_membership_mock_object;
    }

    public function testFindAll()
    {
        // Test for FindAll Method - optionally accepts User_id, Group_id, or assignable as parameter
        $this->mock->expects($this->any())->method('findAll')->withConsecutive(array(), array($this->greaterThan(0)),
            array($this->greaterThan(0)),
            array($this->arrayHasKey('assignable')))->will($this->returnValue($this->group_memberships));

        // Run Test with No parameter
        $groupMemberships = $this->mock->findAll();
        $this->assertEquals(is_object($groupMemberships), true, 'Should return an object');
        $this->assertEquals(is_array($groupMemberships->group_memberships), true,
            'Should return an object containing an array called "group_memberships"');
        $this->assertGreaterThan(0, $groupMemberships->group_memberships[0]->id,
            'Returns a non-numeric id for groups[0]');

        // Run Test with User ID parameter set
        $groupMemberships = $this->mock->findAll($this->group_membership->user_id);
        $this->assertEquals(is_object($groupMemberships), true, 'Should return an object');
        $this->assertEquals(is_array($groupMemberships->group_memberships), true,
            'Should return an object containing an array called "group_memberships"');
        $this->assertGreaterThan(0, $groupMemberships->group_memberships[0]->id,
            'Returns a non-numeric id for groups[0]');

        // Run Test with Group ID parameter set
        $groupMemberships = $this->mock->findAll($this->group_membership->group_id);
        $this->assertEquals(is_object($groupMemberships), true, 'Should return an object');
        $this->assertEquals(is_array($groupMemberships->group_memberships), true,
            'Should return an object containing an array called "group_memberships"');
        $this->assertGreaterThan(0, $groupMemberships->group_memberships[0]->id,
            'Returns a non-numeric id for groups[0]');

        // Run Test with Assignable parameter set
        $groupMemberships = $this->mock->findAll(array("assignable" => true));
        $this->assertEquals(is_object($groupMemberships), true, 'Should return an object');
        $this->assertEquals(is_array($groupMemberships->group_memberships), true,
            'Should return an object containing an array called "group_memberships"');
        $this->assertGreaterThan(0, $groupMemberships->group_memberships[0]->id,
            'Returns a non-numeric id for groups[0]');
    }

    public function testFind()
    {
        // Test for Find Method
        $this->mock->expects($this->any())->method('find')->with($this->greaterThan(0))->will($this->returnValue($this->find_membership));

        $groupMembership = $this->mock->find($this->find_membership->group_membership->id);
        $this->assertEquals(is_object($groupMembership), true, 'Should return an object');
        $this->assertEquals(is_object($groupMembership->group_membership), true,
            'Should return an object containing an object called "group_membership"');
        $this->assertGreaterThan(0, $groupMembership->group_membership->id, 'Returns a non-numeric id for group');
    }

    public function create()
    {
        // Test for Create Method
        $this->mock->expects($this->any())->method('create')->with($this->greaterThan(0),
            $this->greaterThan(0))->will($this->returnValue($this->group_membership));

        $groupMembership = $this->mock->create($this->group_membership->user_id, $this->group_membership->group_id);
        $this->assertEquals(is_object($groupMembership), true, 'Should return an object');
        $this->assertEquals(is_object($groupMembership->group_membership), true,
            'Should return an object called "group_membership"');
        $this->assertGreaterThan(0, $groupMembership->group_membership->id,
            'Returns a non-numeric id for group_membership');
    }

    public function testDelete()
    {
        // Test for Delete Method
        $this->mock->expects($this->any())->method('delete')->with($this->greaterThan(0))->will($this->returnValue(null));

        $groupMembership = $this->mock->delete($this->group_membership->id);
        $this->assertEquals(null, $groupMembership, 'Does not return a null object');
    }

    public function testMakeDefault()
    {
        // Test for MakeDefault Method
        $this->mock->expects($this->any())->method('makeDefault')->with($this->greaterThan(0),
            $this->greaterThan(0))->will($this->returnValue($this->group_memberships));

        $groupMemberships = $this->mock->makeDefault($this->group_membership->user_id, $this->group_membership->id);
        $this->assertEquals(is_object($groupMemberships), true, 'Should return an object');
        $this->assertEquals(is_array($groupMemberships->group_memberships), true,
            'Should return an object containing an array called "group_memberships"');
        $this->assertGreaterThan(0, $groupMemberships->group_memberships[0]->id,
            'Returns a non-numeric id for groups[0]');
    }
}
