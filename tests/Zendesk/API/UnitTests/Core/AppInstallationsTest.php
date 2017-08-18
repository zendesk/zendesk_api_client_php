<?php

namespace Zendesk\API\UnitTests\Core;

use Faker\Factory;
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
                'name' => 'Helpful App - Updated',
                'api_token' => '659323ngt4ut9an'
            ]
        ];

        $this->assertEndpointCalled(function () use ($postParams) {
            $this->client->appInstallations()->create($postParams);
        }, 'apps/installations.json', 'POST', ['postFields' => $postParams]);
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
        $resourceId = 2727;

        $this->assertEndpointCalled(function () use ($resourceId, $queryParams) {
            $this->client->appInstallations()->requirements($resourceId, $queryParams);
        }, "apps/installations/{$resourceId}/requirements.json", 'GET', ['queryParams' => $queryParams]);
    }


    /**
     * Tests if the client calls the correct endpoint for installing an app
     */
    public function testCreate()
    {
        $faker = Factory::create();
        $params = [
            'app_id' => $faker->randomNumber(),
            'settings' => [
                $faker->word => $faker->boolean(),
                $faker->word => $faker->randomNumber(),
            ]
        ];

        $this->assertEndpointCalled(function () use ($params) {
            $this->client->appInstallations()->create($params);
        }, "apps/installations.json", 'POST', ['postFields' => $params]);
    }

    /**
     * Tests if the client calls the correct endpoint for installing an app
     */
    public function testUpdate()
    {
        $faker = Factory::create();
        $params = [
            'enabled' => $faker->boolean(),
            'settings' => [
                $faker->word => $faker->boolean(),
                $faker->word => $faker->randomNumber(),
            ]
        ];

        $id = $faker->randomNumber(null, true);

        $this->assertEndpointCalled(function () use ($id, $params) {
            $this->client->appInstallations()->update($id, $params);
        }, "apps/installations/{$id}.json", 'PUT', ['postFields' => $params]);
    }
}
