<?php

namespace Zendesk\API\UnitTests;

/**
 * Requests test class
 */
class RequestsTest extends BasicTest
{
    public function testFindAll()
    {
        $this->assertEndpointCalled(function () {
            $this->client->requests()->findAll();
        }, 'requests.json');
    }

    public function testFindAllOpen()
    {
        $this->assertEndpointCalled(function () {
            $this->client->requests()->findAllOpen();
        }, 'requests/open.json');
    }

    public function testFindAllSolved()
    {
        $this->assertEndpointCalled(function () {
            $this->client->requests()->findAllSolved();
        }, 'requests/solved.json');
    }

    public function testFindAllCCd()
    {
        $this->assertEndpointCalled(function () {
            $this->client->requests()->findAllCCd();
        }, 'requests/ccd.json');
    }

    public function testFindAllChainedUser()
    {
        $userId = 8373;

        $this->assertEndpointCalled(function () use ($userId) {
            $this->client->users($userId)->requests()->findAll();
        }, "users/{$userId}/requests.json");
    }

    public function testFindAllChainedOrganization()
    {
        $organizationId = 8373;

        $this->assertEndpointCalled(function () use ($organizationId) {
            $this->client->organizations($organizationId)->requests()->findAll();
        }, "organizations/{$organizationId}/requests.json");
    }

    public function testSearch()
    {
        $queryParams = [
            'query'           => 'camera',
            'organization_id' => 1,
        ];

        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->requests()->search($queryParams);
        }, 'requests/search.json', 'GET', ['queryParams' => $queryParams]);
    }

    public function testFind()
    {
        $resourceId = 3838;

        $this->assertEndpointCalled(function () use ($resourceId) {
            $this->client->requests()->find($resourceId);
        }, "requests/{$resourceId}.json");
    }
}
