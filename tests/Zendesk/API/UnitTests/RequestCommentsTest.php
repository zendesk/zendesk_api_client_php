<?php

namespace Zendesk\API\UnitTests;

/**
 * Requests test class
 */
class RequestCommentsTest extends BasicTest
{
    public function testFindAll()
    {
        $requestId = 3838;

        $this->assertEndpointCalled(function () use ($requestId) {
            $this->client->requests($requestId)->comments()->findAll();
        }, "requests/{$requestId}/comments.json");
    }

    public function testFind()
    {
        $resourceId = 3838;
        $requestId  = 19192;

        $this->assertEndpointCalled(function () use ($requestId, $resourceId) {
            $this->client->requests($requestId)->comments()->find($resourceId);
        }, "requests/{$requestId}/comments/{$resourceId}.json");
    }
}
