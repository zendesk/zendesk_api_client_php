<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Class OrganizationMembershipsTest
 */
class OrganizationMembershipsTest extends BasicTest
{
    /**
     * Test find method
     */
    public function testFind()
    {
        $resourceId = 299283;

        $this->assertEndpointCalled(function () use ($resourceId) {
            $this->client->organizationMemberships($resourceId)->find();
        }, "organization_memberships/{$resourceId}.json");
    }

    /**
     * Test find method with chained user
     */
    public function testFindUserOrganizationMemberships()
    {
        $resourceId = 299283;
        $userId     = 28261;

        $this->assertEndpointCalled(function () use ($resourceId, $userId) {
            $this->client->users($userId)->organizationMemberships()->find($resourceId);
        }, "users/{$userId}/organization_memberships/{$resourceId}.json");
    }

    /**
     * Test find with organization memberships is passed when instantiating the resource.
     */
    public function testFindUserOrganizationMembershipsChained()
    {
        $resourceId = 299283;
        $userId     = 28261;

        $this->assertEndpointCalled(function () use ($userId, $resourceId) {
            $this->client->users($userId)->organizationMemberships($resourceId)->find();
        }, "users/{$userId}/organization_memberships/{$resourceId}.json");
    }

    /**
     * Test findAll method
     */
    public function testFindAll()
    {
        $this->assertEndpointCalled(function () {
            $this->client->organizationMemberships()->findAll();
        }, 'organization_memberships.json');
    }

    /**
     * Test findAll with user resource chained
     */
    public function testFindAllUserOrganizationMemberships()
    {
        $userId = 123;

        $this->assertEndpointCalled(function () use ($userId) {
            $this->client->users($userId)->organizationMemberships()->findAll();
        }, "users/{$userId}/organization_memberships.json");
    }

    /**
     * Test findAll with organizations chained
     */
    public function testFindAllOrganizationMemberships()
    {
        $resourceId = 123;
        $this->assertEndpointCalled(function () use ($resourceId) {
            $this->client->organizations($resourceId)->memberships()->findAll();
        }, "organizations/{$resourceId}/organization_memberships.json");
    }

    /**
     * Test create endpoint
     */
    public function testCreateOrganizationMemberships()
    {
        $postFields = [
            'id'              => 461,
            'user_id'         => 72,
            'organization_id' => 88,
            'default'         => true,
            'created_at'      => '2012-04-03T12:34:01Z',
            'updated_at'      => '2012-04-03T12:34:01Z'
        ];

        $this->assertEndpointCalled(
            function () use ($postFields) {
                $this->client->organizationMemberships()->create($postFields);
            },
            'organization_memberships.json',
            'POST',
            [
                'postFields' => ['organization_membership' => $postFields],
            ]
        );
    }

    /**
     * Test create endpoint with user resource chained
     */
    public function testCreateUserOrganizationMemberships()
    {
        $userId     = 282;
        $postFields = [
            'id'              => 461,
            'user_id'         => 72,
            'organization_id' => 88,
            'default'         => true,
            'created_at'      => '2012-04-03T12:34:01Z',
            'updated_at'      => '2012-04-03T12:34:01Z'
        ];

        $this->assertEndpointCalled(
            function () use ($userId, $postFields) {
                $this->client->users($userId)->organizationMemberships()->create($postFields);
            },
            "users/{$userId}/organization_memberships.json",
            'POST',
            [
                'postFields' => ['organization_membership' => $postFields],
            ]
        );
    }

    /**
     * Test createMany method
     */
    public function testCreateMany()
    {
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

        $this->assertEndpointCalled(
            function () use ($postFields) {
                $this->client->organizationMemberships()->createMany($postFields);
            },
            'organization_memberships/create_many.json',
            'POST',
            [
                'postFields' => ['organization_memberships' => $postFields],
            ]
        );
    }

    /**
     * Test delete method
     */
    public function testDelete()
    {
        $resourceId = 299283;

        $this->assertEndpointCalled(function () use ($resourceId) {
            $this->client->organizationMemberships($resourceId)->delete();
        }, "organization_memberships/{$resourceId}.json", 'DELETE');
    }

    /**
     * Test delete method with chained user resource
     */
    public function testDeleteUserOrganizationMemberships()
    {
        $resourceId = 299283;
        $userId     = 28261;

        $this->assertEndpointCalled(function () use ($userId, $resourceId) {
            $this->client->users($userId)->organizationMemberships()->delete($resourceId);
        }, "users/{$userId}/organization_memberships/{$resourceId}.json", 'DELETE');
    }

    /**
     * Test delete with chained user resource and passing the membership id when instantiating.
     */
    public function testDeleteUserOrganizationMembershipsChained()
    {
        $resourceId = 299283;
        $userId     = 28261;

        $this->assertEndpointCalled(function () use ($resourceId, $userId) {
            $this->client->users($userId)->organizationMemberships($resourceId)->delete();
        }, "users/{$userId}/organization_memberships/{$resourceId}.json", 'DELETE');
    }

    /**
     * Test delete many method
     */
    public function testDeleteMany()
    {
        $resourceIds = [299283, 2331];

        $this->assertEndpointCalled(
            function () use ($resourceIds) {
                $this->client->organizationMemberships()->deleteMany($resourceIds);
            },
            "organization_memberships/destroy_many.json",
            'DELETE',
            [
                'queryParams' => ['ids' => implode(',', $resourceIds)],
            ]
        );
    }

    /**
     * Test for make default method
     */
    public function testMakeDefault()
    {
        $params = [
            'id'     => 1122,
            'userId' => 2341,
        ];

        $this->assertEndpointCalled(
            function () use ($params) {
                $this->client->organizationMemberships()->makeDefault($params);
            },
            "users/{$params['userId']}/organization_memberships/{$params['id']}/make_default.json",
            'PUT'
        );
    }

    /**
     * Test for make default method using chaining
     */
    public function testMakeDefaultChained()
    {
        $params = [
            'id'     => 1122,
            'userId' => 2341,
        ];

        $this->assertEndpointCalled(function () use ($params) {
            $this->client->users($params['userId'])->organizationMemberships($params['id'])->makeDefault();
        }, "users/{$params['userId']}/organization_memberships/{$params['id']}/make_default.json", 'PUT');
    }
}
