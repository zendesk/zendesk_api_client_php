<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Class AppInstallationsTest
 */
class AppInstallationsTest extends BasicTest
{
    /**
     * Tests if the endpoint is correct since the format is app/installations
     */
    public function testResourceNameIsCorrectRoute()
    {
        $resourceId = 1;

        $this->assertEndpointCalled(function () use ($resourceId) {
            $this->client->appInstallations()->find($resourceId);
        }, "apps/installations/{$resourceId}.json");

        $postParams = [
            'settings' => [
                'name'      => 'Helpful App - Updated',
                'api_token' => '659323ngt4ut9an'
            ]
        ];

        $this->assertEndpointCalled(function () use ($postParams) {
            $this->client->appInstallations()->create($postParams);
        }, 'apps/installations.json', 'POST', ['postFields' => ['installation' => $postParams]]);

    }

    /**
     * Tests if the client calls the correct endpoint for App Installation job statuses
     */
    public function testJobStatuses()
    {
        $resourceId = 1;
        $this->assertEndpointCalled(function () use ($resourceId) {
            $this->client->appInstallations()->jobStatuses($resourceId);
        }, "apps/installations/job_statuses/{$resourceId}.json");
    }

    /**
     * Tests if the client calls the correct endpoint and adds query parameters for App Installation requirements
     */
    public function testRequirements()
    {
        $queryParams = ['per_page' => 50];
        $resourceId  = 2727;

        $this->assertEndpointCalled(function () use ($resourceId, $queryParams) {
            $this->client->appInstallations()->requirements($resourceId, $queryParams);
        }, "apps/installations/{$resourceId}/requirements.json", 'GET', ['queryParams' => $queryParams]);
    }
}
