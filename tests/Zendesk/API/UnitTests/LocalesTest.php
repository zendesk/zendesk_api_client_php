<?php

namespace Zendesk\API\UnitTests;

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
        $this->endpointTest('GET', 'findAllPublic', 'locales/public.json');
    }

    /**
     * Test findAllAgent method
     */
    public function testFindAllAgent()
    {
        $this->endpointTest('GET', 'findAllAgent', 'locales/agent.json');
    }

    /**
     * Test findCurrent method
     */
    public function testFindCurrent()
    {
        $this->endpointTest('GET', 'findCurrent', 'locales/current.json');
    }

    /**
     * Test findBest method
     */
    public function testFindBest()
    {
        $this->endpointTest('GET', 'findBest', 'locales/detect_best_locale.json');
    }
}
