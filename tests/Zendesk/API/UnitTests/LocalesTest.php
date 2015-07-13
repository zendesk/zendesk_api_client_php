<?php

namespace Zendesk\API\UnitTests;

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
        $this->endpointTest('GET', ['locales', 'findAllPublic'], 'locales/public.json');
    }

    /**
     * Test findAllAgent method
     */
    public function testFindAllAgent()
    {
        $this->endpointTest('GET', ['locales', 'findAllAgent'], 'locales/agent.json');
    }

    /**
     * Test findCurrent method
     */
    public function testFindCurrent()
    {
        $this->endpointTest('GET', ['locales', 'findCurrent'], 'locales/current.json');
    }

    /**
     * Test findBest method
     */
    public function testFindBest()
    {
        $this->endpointTest('GET', ['locales', 'findBest'], 'locales/detect_best_locale.json');
    }
}
