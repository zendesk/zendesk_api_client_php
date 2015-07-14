<?php

namespace Zendesk\API\UnitTests;

use GuzzleHttp\Psr7\Response;

/**
 * Locals test class
 */
class LocalsTest extends BasicTest
{
    /**
     * Test findAllPublic method
     */
    public function testFindAllPublic()
    {
        $this->getEndpointTest('findAllPublic', 'locales/public.json');
    }

    /**
     * Test findAllAgent method
     */
    public function testFindAllAgent()
    {
        $this->getEndpointTest('findAllAgent', 'locales/agent.json');
    }

    /**
     * Test findCurrent method
     */
    public function testFindCurrent()
    {
        $this->getEndpointTest('findCurrent', 'locales/current.json');
    }

    /**
     * Test findBest method
     */
    public function testFindBest()
    {
        $this->getEndpointTest('findBest', 'locales/detect_best_locale.json');
    }

    /**
     * Test for the get endpoint using the given method and endpoint
     *
     * @param $method
     * @param $endpoint
     */
    private function getEndpointTest($method, $endpoint)
    {
        $this->mockAPIResponses([
            new Response(200, [], '')
        ]);

        $this->client->locales()->$method();

        $this->assertLastRequestIs(
            [
                'method'   => 'GET',
                'endpoint' => $endpoint,
            ]
        );
    }
}
