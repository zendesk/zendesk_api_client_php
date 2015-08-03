<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * UserFields test class
 */
class UserFieldsTest extends BasicTest
{
    /**
     * Tests if the client can call and build the reorder endpoint
     */
    public function testReorder()
    {
        $ids = [1, 2, 3];

        $this->assertEndpointCalled(function () use ($ids) {
            $this->client->userFields()->reorder($ids);
        }, 'user_fields/reorder.json', 'PUT', ['postFields' => ['user_field_ids' => $ids]]);
    }
}
