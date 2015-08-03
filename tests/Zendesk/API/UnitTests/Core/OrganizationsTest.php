<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Class OrganizationsTest
 */
class OrganizationsTest extends BasicTest
{
    /**
     * test for FindAll with chained user resource.
     */
    public function testFindUserOrganizations()
    {
        $userId = 232;
        $this->assertEndpointCalled(function () use ($userId) {
            $this->client->users($userId)->organizations()->findAll();
        }, "users/{$userId}/organizations.json");
    }

    /**
     * Tests if the default findAll route is still accessible
     */
    public function testFindAllOrganizations()
    {
        $this->assertEndpointCalled(function () {
            $this->client->organizations()->findAll();
        }, 'organizations.json');
    }

    /**
     * Test for autocomplete method
     */
    public function testAutocomplete()
    {
        $this->assertEndpointCalled(function () {
            $this->client->organizations()->autocomplete('foo');
        }, 'organizations/autocomplete.json', 'GET', ['queryParams' => ['name' => 'foo']]);
    }

    /**
     * Test related method
     */
    public function testRelated()
    {
        $resourceId = 123;
        $this->assertEndpointCalled(function () use ($resourceId) {
            $this->client->organizations()->related($resourceId);
        }, "organizations/{$resourceId}/related.json");
    }

    /**
     * Test for search method
     */
    public function testSearchByExternalId()
    {
        $externalId = 123;
        $this->assertEndpointCalled(function () use ($externalId) {
            $this->client->organizations()->search($externalId);
        }, 'organizations/search.json', 'GET', ['queryParams' => ['external_id' => 123]]);
    }
}
