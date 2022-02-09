<?php

namespace ZEndesk\Api\UnitTests\Core;

use Zendesk\Api\UnitTests\BasicTest;

/**
 * Webhooks test class
 */
class WebhooksTest extends BasicTest
{
    /**
     * Test find all method
     */

    public function testFindAll()
    {
        $queryParams = [
            'filter[name_contains]' => 'somerule',
        ];

        // We expect invalid parameters are removed.
        // We also expect url encoded keys and values
        $expectedQueryParams = [];
        foreach ($queryParams as $key => $value) {
            $expectedQueryParams = array_merge($expectedQueryParams, [urlencode($key) => $value]);
        }

        $this->assertEndpointCalled(
            function () use ($queryParams) {
                $this->client->webhooks()->findAll($queryParams);
            },
            'webhooks',
            'GET',
            ['queryParams' => $expectedQueryParams]
        );
    }
}
