<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

/**
 * Triggers test class
 */
class TriggersTest extends BasicTest
{
    public function testActive()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->triggers()->findActive();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'triggers/active.json',
            ]
        );
    }
}
