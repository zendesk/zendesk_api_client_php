<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;
use Zendesk\API\Resources\AppInstallations;

class AppInstallationsTest extends BasicTest
{
    /**
     * Tests if the endpoint is correct since the format is app/installations
     */
    public function testResourceNameIsCorrectRoute()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->appInstallations()->find(1);

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'apps/installations/1.json'
            ]
        );

        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $postParams = ['settings' => ['name' => 'Helpful App - Updated', 'api_token' => '659323ngt4ut9an']];

        $this->client->appInstallations()->create($postParams);

        $this->assertLastRequestIs(
            [
                'method'     => 'POST',
                'endpoint'   => 'apps/installations.json',
                'postFields' => [AppInstallations::OBJ_NAME => $postParams]
            ]
        );
    }

    /**
     * Tests if the client calls the correct endpoint for App Installation job statuses
     */
    public function testJobStatuses()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->appInstallations()->jobStatuses(1);

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'apps/installations/job_statuses/1.json'
            ]
        );
    }

    /**
     * Tests if the client calls the correct endpoint and adds query parameters for App Installation requirements
     */
    public function testRequirements()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->appInstallations()->requirements(1, ['per_page' => 50]);

        $this->assertLastRequestIs(
            [
                'method'      => 'GET',
                'endpoint'    => 'apps/installations/1/requirements.json',
                'queryParams' => ['per_page' => 50]
            ]
        );
    }
}
