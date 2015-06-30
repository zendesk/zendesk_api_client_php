<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

/**
 * Automations test class
 */
class AutomationsTest extends BasicTest
{

    /**
     * Test we can use endpoint to get active automations
     */
    public function testActive()
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->automations()->findActive();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => 'automations/active.json',
            ]
        );
    }
}
