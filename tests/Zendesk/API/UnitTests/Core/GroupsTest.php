<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Class GroupsTest
 */
class GroupsTest extends BasicTest
{
    /**
     * Test the assignable method
     */
    public function testAssignableEndpoint()
    {
        $this->assertEndpointCalled(function () {
            $this->client->groups()->assignable();
        }, 'groups/assignable.json');
    }

    /**
     * Test finding of user groups
     */
    public function testFindUserGroups()
    {
        $this->assertEndpointCalled(function () {
            $this->client->users(123)->groups()->findAll();
        }, 'users/123/groups.json');
    }

    /**
     * Tests if the default findAll route is still accessible
     */
    public function testFindAllGroups()
    {
        $this->assertEndpointCalled(function () {
            $this->client->groups()->findAll();
        }, 'groups.json');
    }
}
