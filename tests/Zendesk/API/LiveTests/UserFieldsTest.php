<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\HttpClient;

/**
 * UserFields test class
 */
class UserFieldsTest extends BasicTest
{
    public function testReorder()
    {
        $this->mockApiCall('PUT', '/user_fields/reorder.json', []);
        $this->client->userFields()->reorder(array([123, 456, 789]));
    }
}
