<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

/**
 * OrganizationFields test class
 */
class OrganizationFieldsTest extends BasicTest
{
    public function testReorder()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $postFields = ['organization_field_ids' => [14382, 14342]];

        $this->client->organizationFields()->reorder($postFields);

        $this->assertLastRequestIs(
            [
                'method'     => 'PUT',
                'endpoint'   => 'organization_fields/reorder.json',
                'postFields' => $postFields,
            ]
        );
    }
}
