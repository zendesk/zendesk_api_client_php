<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\UnitTests\BasicTest;

/**
 * Test for GroupMemberships
 */
class GroupMembershipsTest extends BasicTest
{
    /**
     * Test the list endpoint, since this resource involves chaining
     */
    public function testListEndpoint()
    {
        $this->mockAPIResponses([
            new Response(200, [], ''),
            new Response(200, [], ''),
            new Response(200, [], ''),
        ]);

        // GET /api/v2/groups/{group_id}/memberships.json
        $this->client->groups(123)->memberships()->findAll();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'groups/123/memberships.json',
        ]);

        // GET /api/v2/group_memberships.json
        $this->client->groupMemberships()->findAll();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'group_memberships.json',
        ]);

        // GET /api/v2/users/{user_id}/group_memberships.json
        $this->client->users(123)->groupMemberships()->findAll();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'users/123/group_memberships.json',
        ]);
    }

    /**
     * Test the assignable endpoint, since this resource involves chaining
     */
    public function testAssignableEndpoint()
    {
        $this->mockAPIResponses([
            new Response(200, [], ''),
            new Response(200, [], ''),
        ]);

        $queryParams = ['per_page' => 20];

        // GET /api/v2/groups/{group_id}/memberships/assignable.json
        $this->client->groups(123)->memberships()->assignable($queryParams);

        $this->assertLastRequestIs([
            'method'      => 'GET',
            'endpoint'    => 'groups/123/memberships/assignable.json',
            'queryParams' => $queryParams
        ]);

        // GET /api/v2/group_memberships/assignable.json
        $this->client->groupMemberships()->assignable($queryParams);

        $this->assertLastRequestIs([
            'method'      => 'GET',
            'endpoint'    => 'group_memberships/assignable.json',
            'queryParams' => $queryParams
        ]);
    }

    /**
     * Test the show endpoint, since this resource involves chaining
     */
    public function testFindEndpoint()
    {
        $this->mockAPIResponses([
            new Response(200, [], ''),
            new Response(200, [], ''),
            new Response(200, [], ''),
        ]);

        // GET /api/v2/users/{user_id}/group_memberships/{id}.json
        $this->client->users(123)->groupMemberships()->find(456);

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'users/123/group_memberships/456.json',
        ]);

        // GET /api/v2/users/{user_id}/group_memberships/{id}.json
        $this->client->users(123)->groupMemberships(456)->find();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'users/123/group_memberships/456.json',
        ]);

        // GET /api/v2/group_memberships/{id}.json
        $this->client->groupMemberships()->find(123);

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'group_memberships/123.json',
        ]);
    }

    /**
     * Test the create endpoint, since this resource involves chaining
     */
    public function testCreateEndpoint()
    {
        $this->mockAPIResponses([
            new Response(200, [], ''),
            new Response(200, [], ''),
        ]);

        $postData = ['user_id' => 72, 'group_id' => 88];

        // POST /api/v2/users/{user_id}/group_memberships.json
        $this->client->users(123)->groupMemberships()->create($postData);

        $this->assertLastRequestIs([
            'method'     => 'POST',
            'endpoint'   => 'users/123/group_memberships.json',
            'postFields' => ['group_membership' => $postData]
        ]);

        // POST /api/v2/group_memberships/{id}.json
        $this->client->groupMemberships()->create($postData);

        $this->assertLastRequestIs([
            'method'     => 'POST',
            'endpoint'   => 'group_memberships.json',
            'postFields' => ['group_membership' => $postData]
        ]);
    }


    /**
     * Test the delete endpoint, since this resource involves chaining
     */
    public function testDeleteEndpoint()
    {
        $this->mockAPIResponses([
            new Response(200, [], ''),
            new Response(200, [], ''),
            new Response(200, [], ''),
        ]);

        // DELETE api/v2/users/{user_id}/group_memberships/{id}.json
        $this->client->users(123)->groupMemberships()->delete(456);

        $this->assertLastRequestIs([
            'method'   => 'DELETE',
            'endpoint' => 'users/123/group_memberships/456.json',
        ]);

        // DELETE /api/v2/group_memberships/{id}.json
        $this->client->groupMemberships()->delete(123);

        $this->assertLastRequestIs([
            'method'   => 'DELETE',
            'endpoint' => 'group_memberships/123.json'
        ]);

        // DELETE /api/v2/group_memberships/{id}.json
        $this->client->groupMemberships()->deleteMany([1, 2, 3]);

        $this->assertLastRequestIs([
            'method'      => 'DELETE',
            'endpoint'    => 'group_memberships/destroy_many.json',
            'queryParams' => ['ids' => '1,2,3']
        ]);
    }

    public function testMakeDefault()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $params = [
            'id'     => 1122,
            'userId' => 2341,
        ];

        $this->client->groupMemberships()->makeDefault($params);

        $this->assertLastRequestIs([
            'method'   => 'PUT',
            'endpoint' => "users/{$params['userId']}/group_memberships/{$params['id']}/make_default.json",
        ]);
    }

    public function testMakeDefaultChained()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $params = [
            'id'     => 1122,
            'userId' => 2341,
        ];

        $this->client->users($params['userId'])->groupMemberships($params['id'])->makeDefault();

        $this->assertLastRequestIs([
            'method'   => 'PUT',
            'endpoint' => "users/{$params['userId']}/group_memberships/{$params['id']}/make_default.json",
        ]);
    }
}
