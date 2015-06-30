<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

/**
 * UserFields test class
 */
class UserFieldsTest extends BasicTest
{
    public function testReorder()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $ids = [1, 2, 3];

        $this->client->userFields()->reorder($ids);

        $this->assertLastRequestIs(
            [
                'method'     => 'PUT',
                'endpoint'   => 'user_fields/reorder.json',
                'postFields' => ['user_field_ids' => $ids],
            ]
        );
    }
}
