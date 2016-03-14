<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Class OrganizationSubscriptionsTest
 */
class OrganizationSubscriptionsTest extends BasicTest
{
    /**
     * Test that the resource name is set correctly
     */
    public function testResourceNameIsCorrect()
    {
        $resourceName = $this->client->organizationSubscriptions()->getResourceName();

        $this->assertEquals(
            'organization_subscriptions',
            $resourceName,
            'Should return `organization_subscriptions` as resource name'
        );
    }

    /**
     * Test find method with chained user resource
     */
    public function testFindUserOrganizations()
    {
        $userId = 82828;
        $this->assertEndpointCalled(function () use ($userId) {
            $this->client->users($userId)->organizationSubscriptions()->findAll();
        }, "users/{$userId}/organization_subscriptions.json");
    }

    /**
     * Test find method with chained organization resource
     */
    public function testFindOrganizationSubscriptions()
    {
        $organizationId = 9393;
        $this->assertEndpointCalled(function () use ($organizationId) {
            $this->client->organizations($organizationId)->subscriptions()->findAll();
        }, "organizations/{$organizationId}/subscriptions.json");
    }

    /**
     * Tests if the default findAll route is still accessible
     */
    public function testFindAllOrganizationSubscriptions()
    {
        $this->assertEndpointCalled(function () {
            $this->client->organizationSubscriptions()->findAll();
        }, 'organization_subscriptions.json');
    }
}
