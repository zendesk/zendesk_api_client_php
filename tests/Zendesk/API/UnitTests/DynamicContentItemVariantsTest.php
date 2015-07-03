<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

class DynamicContentItemVariantsTest extends BasicTest
{
    public function testItemIdIsAddedToRoute()
    {
        // Test the chaining
        $this->mockAPIResponses([new Response(200, [], '')]);

        $this->client->dynamicContent()->items(12345)->variants()->findAll();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'dynamic_content/items/12345/variants.json',
            ]
        );
    }

    public function testItemIdVariantIdIsAddedToRoute()
    {
        // Test the chaining
        $this->mockAPIResponses([new Response(200, [], '')]);

        $this->client->dynamicContent()->items(12345)->variants()->find(2);

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'dynamic_content/items/12345/variants/2.json',
            ]
        );
    }
}
