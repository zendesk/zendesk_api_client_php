<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Class AppInstallationLocationsTest
 */
class AppInstallationLocationsTest extends BasicTest
{

    /**
     * Test get incremental export for tickets
     */
    public function testTraits()
    {
        $this->assertTrue(method_exists($this->client->apps()->installationLocations(), 'find'));
        $this->assertTrue(method_exists($this->client->apps()->installationLocations(), 'findAll'));
    }

    /**
     * Test the reorder method
     *
     */
    public function testReorder()
    {
        $postFields = [
            'installations' => [82, 56],
            'location_name' => 'nav_bar'
        ];

        $this->assertEndpointCalled(function () use ($postFields) {
            $this->client->apps()->installationLocations()->reorder($postFields);
        }, 'apps/location_installations/reorder.json', 'POST', ['postFields' => $postFields]);
    }
}
