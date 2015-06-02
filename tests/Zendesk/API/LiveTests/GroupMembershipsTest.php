<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * GroupMemberships test class
 */
class GroupMembershipsTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $id, $group_id, $user_id, $number;

    public function setUp()
    {
        $this->number = strval(rand(1, 1000));
        /*
         * First start by creating a topic (we'll delete it later)
         */
        $group = $this->client->groups()->create(array(
            'name' => 'New Group'
        ));
        $this->group_id = $group->group->id;

        $user = $this->client->users()->create(array(
            'name' => 'Roger Wilco' . $this->number,
            'email' => 'roge' . $this->number . '@example.org',
            'role' => 'agent',
            'verified' => true
        ));
        $this->user_id = $user->user->id;

        $groupMembership = $this->client->groupMemberships()->create(array(
            'group_id' => $this->group_id,
            'user_id' => $this->user_id
        ));

        $this->assertEquals(is_object($groupMembership), true, 'Should return an object');
        $this->assertEquals(is_object($groupMembership->group_membership), true,
            'Should return an object called "group_membership"');
        $this->assertGreaterThan(0, $groupMembership->group_membership->id,
            'Returns a non-numeric id for group_membership');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $groupMembership->group_membership->id;
    }

    public function testAll()
    {
        $groupMemberships = $this->client->groupMemberships()->findAll();
        $this->assertEquals(is_object($groupMemberships), true, 'Should return an object');
        $this->assertEquals(is_array($groupMemberships->group_memberships), true,
            'Should return an object containing an array called "group_memberships"');
        $this->assertGreaterThan(0, $groupMemberships->group_memberships[0]->id,
            'Returns a non-numeric id for groups[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testAllByUser()
    {
        $groupMemberships = $this->client->user($this->user_id)->groupMemberships()->findAll();
        $this->assertEquals(is_object($groupMemberships), true, 'Should return an object');
        $this->assertEquals(is_array($groupMemberships->group_memberships), true,
            'Should return an object containing an array called "group_memberships"');
        $this->assertGreaterThan(0, $groupMemberships->group_memberships[0]->id,
            'Returns a non-numeric id for groups[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testAllByGroup()
    {
        $groupMemberships = $this->client->group($this->group_id)->members()->findAll();
        $this->assertEquals(is_object($groupMemberships), true, 'Should return an object');
        $this->assertEquals(is_array($groupMemberships->group_memberships), true,
            'Should return an object containing an array called "group_memberships"');
        $this->assertGreaterThan(0, $groupMemberships->group_memberships[0]->id,
            'Returns a non-numeric id for groups[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $groupMembership = $this->client->groupMembership($this->id)->find(); // don't delete group membership #22534232
        $this->assertEquals(is_object($groupMembership), true, 'Should return an object');
        $this->assertGreaterThan(0, $groupMembership->group_membership->id, 'Returns a non-numeric id for group');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testMakeDefault()
    {
        $groupMemberships = $this->client->user($this->user_id)->groupMembership($this->id)->makeDefault();
        $this->assertEquals(is_object($groupMemberships), true, 'Should return an object');
        $this->assertEquals(is_array($groupMemberships->group_memberships), true,
            'Should return an object containing an array called "group_memberships"');
        $this->assertGreaterThan(0, $groupMemberships->group_memberships[0]->id,
            'Returns a non-numeric id for groups[0]');
    }

    public function tearDown()
    {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a group membership id to test with. Did setUP fail?');
        $view = $this->client->groupMembership($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        /*
         * Clean-up
         */
        $this->client->group($this->group_id)->delete();
        $this->client->user($this->user_id)->delete();
    }

}
