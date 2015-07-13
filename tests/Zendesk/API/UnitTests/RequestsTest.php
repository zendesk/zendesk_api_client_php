<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

/**
 * Requests test class
 */
class RequestsTest extends BasicTest
{
    public function testFindAll()
    {
        $this->assertEndpointCalled(['requests', 'findAll'], ['GET', 'requests.json']);
    }

    public function testFindAllOpen()
    {
        $this->assertEndpointCalled(['requests', 'findAllOpen'], ['GET', 'requests/open.json']);
    }

    public function testFindAllSolved()
    {
        $this->assertEndpointCalled(['requests', 'findAllSolved'], ['GET', 'requests/solved.json']);
    }

    public function testFindAllCCd()
    {
        $this->assertEndpointCalled(['requests', 'findAllCCd'], ['GET', 'requests/ccd.json']);
    }

    public function testFindAllChainedUser()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $userId = 8373;

        $this->client->users($userId)->requests()->findAll();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => "users/{$userId}/requests.json",
            ]
        );
    }

    public function testFindAllChainedOrganization()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $organizationId = 8373;

        $this->client->organizations($organizationId)->requests()->findAll();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => "organizations/{$organizationId}/requests.json",
            ]
        );
    }

    public function testSearch()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $queryParams = [
            'query'           => 'camera',
            'organization_id' => 1,
        ];

        $this->client->requests()->search($queryParams);

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'requests/search.json',
                'queryParams' => $queryParams
            ]
        );
    }

    public function testFind()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $resourceId = 3838;

        $this->client->requests()->find($resourceId);

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => "requests/{$resourceId}.json",
            ]
        );
    }
}
