<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

class GroupsTest extends BasicTest
{
    public function testAssignableEndpoint()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->groups()->assignable();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'groups/assignable.json',
        ]);
    }

    public function testFindUserGroups()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->users(123)->groups()->findAll();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'users/123/groups.json',
        ]);
    }

    /**
     * Tests if the default findAll route is still accessible
     */
    public function testFindAllGroups()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->groups()->findAll();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'groups.json',
        ]);
    }
}
