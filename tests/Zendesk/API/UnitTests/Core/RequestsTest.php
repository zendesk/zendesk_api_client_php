<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Requests test class
 */
class RequestsTest extends BasicTest
{
    /**
     * test findAll method
     */
    public function testFindAll()
    {
        $this->assertEndpointCalled(function () {
            $this->client->requests()->findAll();
        }, 'requests.json');
    }

    /**
     * Test findAllOpen method
     */
    public function testFindAllOpen()
    {
        $this->assertEndpointCalled(function () {
            $this->client->requests()->findAllOpen();
        }, 'requests/open.json');
    }

    /**
     * Test findAllSolved method
     */
    public function testFindAllSolved()
    {
        $this->assertEndpointCalled(function () {
            $this->client->requests()->findAllSolved();
        }, 'requests/solved.json');
    }

    /**
     * Test findAllCCd method
     */
    public function testFindAllCCd()
    {
        $this->assertEndpointCalled(function () {
            $this->client->requests()->findAllCCd();
        }, 'requests/ccd.json');
    }

    /**
     * Test findAll method with a chained user
     */
    public function testFindAllChainedUser()
    {
        $userId = 8373;

        $this->assertEndpointCalled(function () use ($userId) {
            $this->client->users($userId)->requests()->findAll();
        }, "users/{$userId}/requests.json");
    }

    /**
     * Test findAll method with chained organization
     */
    public function testFindAllChainedOrganization()
    {
        $organizationId = 8373;

        $this->assertEndpointCalled(function () use ($organizationId) {
            $this->client->organizations($organizationId)->requests()->findAll();
        }, "organizations/{$organizationId}/requests.json");
    }

    /**
     * Test search method
     */
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
}
