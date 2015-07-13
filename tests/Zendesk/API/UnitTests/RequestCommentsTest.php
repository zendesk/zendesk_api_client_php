<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

/**
 * Requests test class
 */
class RequestCommentsTest extends BasicTest
{
    public function testFindAll()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $requestId = 3838;

        $this->client->requests($requestId)->comments()->findAll();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => "requests/{$requestId}/comments.json",
            ]
        );
    }

    public function testFind()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $resourceId = 3838;
        $requestId  = 19192;

        $this->client->requests($requestId)->comments()->find($resourceId);

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => "requests/{$requestId}/comments/{$resourceId}.json",
            ]
        );
    }
}
