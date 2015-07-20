<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Locales test class
 */
class LocalesTest extends BasicTest
{
    /**
     * Test findAllPublic method
     */
    public function testFindAllPublic()
    {
        $this->assertEndpointCalled(function () {
            $this->client->locales()->findAllPublic();
        }, 'locales/public.json');
    }

    /**
     * Test findAllAgent method
     */
    public function testFindAllAgent()
    {
        $this->assertEndpointCalled(function () {
            $this->client->locales()->findAllAgent();
        }, 'locales/agent.json');
    }

    /**
     * Test findCurrent method
     */
    public function testFindCurrent()
    {
        $this->assertEndpointCalled(function () {
            $this->client->locales()->findCurrent();
        }, 'locales/current.json');
    }

    /**
     * Test findBest method
     */
    public function testFindBest()
    {
        $this->assertEndpointCalled(function () {
            $this->client->locales()->findBest();
        }, 'locales/detect_best_locale.json');
    }
}
