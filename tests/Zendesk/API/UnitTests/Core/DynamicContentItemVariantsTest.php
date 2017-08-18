<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Class DynamicContentItemVariantsTest
 */
class DynamicContentItemVariantsTest extends BasicTest
{
    /**
     * Test item id is in route
     */
    public function testItemIdIsAddedToRoute()
    {
        $itemId = 12345;
        $this->assertEndpointCalled(function () use ($itemId) {
            $this->client->dynamicContent()->items($itemId)->variants()->findAll();
        }, "dynamic_content/items/{$itemId}/variants.json");
    }

    /**
     * Test variant id is added to route.
     *
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function testItemIdVariantIdIsAddedToRoute()
    {
        $itemId    = 12345;
        $variantId = 3332;
        $this->assertEndpointCalled(function () use ($itemId, $variantId) {
            $this->client->dynamicContent()->items($itemId)->variants()->find($variantId);
        }, "dynamic_content/items/{$itemId}/variants/{$variantId}.json");
    }
}
