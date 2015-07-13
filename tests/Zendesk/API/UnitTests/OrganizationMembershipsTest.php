<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\Resources\OrganizationMemberships;

class OrganizationMembershipsTest extends BasicTest
{
    public function testFind()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);
        $resourceId = 299283;

        $this->client->organizationMemberships($resourceId)->find();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => "organization_memberships/{$resourceId}.json",
        ]);
    }

    public function testFindUserOrganizationMemberships()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);
        $resourceId = 299283;
        $userId     = 28261;

        $this->client->users($userId)->organizationMemberships()->find($resourceId);

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => "users/{$userId}/organization_memberships/{$resourceId}.json",
        ]);
    }

    public function testFindUserOrganizationMembershipsChained()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);
        $resourceId = 299283;
        $userId     = 28261;

        $this->client->users($userId)->organizationMemberships($resourceId)->find();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => "users/{$userId}/organization_memberships/{$resourceId}.json",
        ]);
    }

    public function testFindAll()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->organizationMemberships()->findAll();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'organization_memberships.json',
        ]);
    }

    public function testFindAllUserOrganizationMemberships()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $userId = 123;

        $this->client->users($userId)->organizationMemberships()->findAll();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => "users/{$userId}/organization_memberships.json",
        ]);
    }

    public function testFindAllOrganizationMemberships()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->organizations(123)->organizationMemberships()->findAll();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'organizations/123/organization_memberships.json',
        ]);
    }

    public function testCreateOrganizationMemberships()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);
        $postFields = [
            'id'              => 461,
            'user_id'         => 72,
            'organization_id' => 88,
            'default'         => true,
            'created_at'      => '2012-04-03T12:34:01Z',
            'updated_at'      => '2012-04-03T12:34:01Z'
        ];

        $this->client->organizationMemberships()->create($postFields);

        $this->assertLastRequestIs([
            'method'     => 'POST',
            'endpoint'   => 'organization_memberships.json',
            'postFields' => [OrganizationMemberships::OBJ_NAME => $postFields],
        ]);
    }

    public function testCreateUserOrganizationMemberships()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $userId     = 282;
        $postFields = [
            'id'              => 461,
            'user_id'         => 72,
            'organization_id' => 88,
            'default'         => true,
            'created_at'      => '2012-04-03T12:34:01Z',
            'updated_at'      => '2012-04-03T12:34:01Z'
        ];

        $this->client->users($userId)->organizationMemberships()->create($postFields);

        $this->assertLastRequestIs([
            'method'     => 'POST',
            'endpoint'   => "users/{$userId}/organization_memberships.json",
            'postFields' => [OrganizationMemberships::OBJ_NAME => $postFields],
        ]);
    }

    public function testCreateMany()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $postFields = [
            [
                'user_id'         => 72,
                'organization_id' => 88,
            ],
            [
                'user_id'         => 28,
                'organization_id' => 88,
            ],
        ];

        $this->client->organizationMemberships()->createMany($postFields);

        $this->assertLastRequestIs([
            'method'     => 'POST',
            'endpoint'   => 'organization_memberships/create_many.json',
            'postFields' => [OrganizationMemberships::OBJ_NAME_PLURAL => $postFields],
        ]);
    }

    public function testDelete()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);
        $resourceId = 299283;

        $this->client->organizationMemberships($resourceId)->delete();

        $this->assertLastRequestIs([
            'method'   => 'DELETE',
            'endpoint' => "organization_memberships/{$resourceId}.json",
        ]);
    }

    public function testDeleteUserOrganizationMemberships()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);
        $resourceId = 299283;
        $userId     = 28261;

        $this->client->users($userId)->organizationMemberships()->delete($resourceId);

        $this->assertLastRequestIs([
            'method'   => 'DELETE',
            'endpoint' => "users/{$userId}/organization_memberships/{$resourceId}.json",
        ]);
    }

    public function testDeleteUserOrganizationMembershipsChained()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);
        $resourceId = 299283;
        $userId     = 28261;

        $this->client->users($userId)->organizationMemberships($resourceId)->delete();

        $this->assertLastRequestIs([
            'method'   => 'DELETE',
            'endpoint' => "users/{$userId}/organization_memberships/{$resourceId}.json",
        ]);
    }

    public function testDeleteMany()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);
        $resourceIds = [299283, 2331];

        $this->client->organizationMemberships()->deleteMany($resourceIds);

        $this->assertLastRequestIs([
            'method'      => 'DELETE',
            'endpoint'    => "organization_memberships/destroy_many.json",
            'queryParams' => ['ids' => implode(',', $resourceIds)],
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

        $this->client->organizationMemberships()->makeDefault($params);

        $this->assertLastRequestIs([
            'method'   => 'PUT',
            'endpoint' => "users/{$params['userId']}/organization_memberships/{$params['id']}/make_default.json",
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

        $this->client->users($params['userId'])->organizationMemberships($params['id'])->makeDefault();

        $this->assertLastRequestIs([
            'method'   => 'PUT',
            'endpoint' => "users/{$params['userId']}/organization_memberships/{$params['id']}/make_default.json",
        ]);
    }
}
