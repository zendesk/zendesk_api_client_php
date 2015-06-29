<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

class GroupsTest extends BasicTest
{
    public function testAssignableEndpoint()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->groups()->assignable();

        $this->assertLastRequestIs([
            'method'   => 'GET',
            'endpoint' => 'groups/assignable.json',
        ]);
    }
}
