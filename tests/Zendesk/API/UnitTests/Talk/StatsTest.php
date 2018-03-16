<?php

namespace Zendesk\API\UnitTests\Talk;

use Faker\Factory;
use Zendesk\API\Resources\HelpCenter\Sections;
use Zendesk\API\UnitTests\BasicTest;

class StatsTest extends BasicTest
{
    /**
     * Tests if the current queue endpoint can be called and passed the correct params
     */
    public function testCurrentQueue()
    {
        $this->assertEndpointCalled(function () {
            $this->client->talk->stats()->currentQueue();
        }, 'channels/voice/stats/current_queue_activity.json', 'GET');
    }

    /**
     * Tests if the account overview endpoint can be called and passed the correct params
     */
    public function testAccountOverview()
    {
        $this->assertEndpointCalled(function () {
            $this->client->talk->stats()->accountOverview();
        }, 'channels/voice/stats/account_overview.json', 'GET');
    }

    /**
     * Tests if the agents overview endpoint can be called and passed the correct params
     */
    public function testAgentsOverview()
    {
        $this->assertEndpointCalled(function () {
            $this->client->talk->stats()->agentsOverview();
        }, 'channels/voice/stats/agents_overview.json', 'GET');
    }

    /**
     * Tests if the agents activity endpoint can be called and passed the correct params
     */
    public function testAgentsActivity()
    {
        $this->assertEndpointCalled(function () {
            $this->client->talk->stats()->agentsActivity();
        }, 'channels/voice/stats/agents_activity.json', 'GET');
    }
}
