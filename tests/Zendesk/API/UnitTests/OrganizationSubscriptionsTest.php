<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

class OrganizationSubscriptionsTest extends BasicTest
{
    public function testResourceNameIsCorrect()
    {
        $resourceName = $this->client->organizationSubscriptions()->getResourceName();

        $this->assertEquals(
            'organization_subscriptions',
            $resourceName,
            'Should return `organization_subscriptions` as resource name'
        );
    }

    public function testFindUserOrganizations()
    {
        $this->mockAPIResponses(
            [
                new Response(200, [], '')
            ]
        );

        $this->client->users(123)->organizationSubscriptions()->findAll();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'users/123/organization_subscriptions.json',
            ]
        );
    }

    public function testFindOrganizationSubscriptions()
    {
        $this->mockAPIResponses(
            [
                new Response(200, [], '')
            ]
        );

        $this->client->organizations(123)->subscriptions()->findAll();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'organizations/123/subscriptions.json',
            ]
        );
    }

    /**
     * Tests if the default findAll route is still accessible
     */
    public function testFindAllOrganizationSubscriptions()
    {
        $this->mockAPIResponses(
            [
                new Response(200, [], '')
            ]
        );

        $this->client->organizationSubscriptions()->findAll();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'organization_subscriptions.json',
            ]
        );
    }
}
