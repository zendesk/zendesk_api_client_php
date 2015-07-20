<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Requests test class
 */
class RequestCommentsTest extends BasicTest
{
    /**
     * Test findAll method
     */
    public function testFindAll()
    {
        $requestId = 3838;

        $this->assertEndpointCalled(function () use ($requestId) {
            $this->client->requests($requestId)->comments()->findAll();
        }, "requests/{$requestId}/comments.json");
    }

    /**
     * Test find method
     */
    public function testFind()
    {
        $resourceId = 3838;
        $requestId  = 19192;

        $this->assertEndpointCalled(function () use ($requestId, $resourceId) {
            $this->client->requests($requestId)->comments()->find($resourceId);
        }, "requests/{$requestId}/comments/{$resourceId}.json");
    }
}
