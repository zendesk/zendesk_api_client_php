<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Brands test class
 */
class BrandsTest extends BasicTest
{
    /**
     * Test that the correct traits were added by checking the available methods
     */
    public function testMethods()
    {
        $this->assertTrue(method_exists($this->client->brands(), 'create'));
        $this->assertTrue(method_exists($this->client->brands(), 'delete'));
        $this->assertTrue(method_exists($this->client->brands(), 'find'));
        $this->assertTrue(method_exists($this->client->brands(), 'findAll'));
        $this->assertTrue(method_exists($this->client->brands(), 'update'));
    }

    /**
     * Test endpoint to check host mapping is available
     */
    public function testCheckHostMapping()
    {
        $queryParams = [
            'host_mapping' => 'test.com',
            'subdomain'    => 'test',
        ];

        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->brands()->checkHostMapping($queryParams);
        }, 'brands/check_host_mapping.json', 'GET', ['queryParams' => $queryParams]);
    }

    /**
     * Test updateImage method
     */
    public function testUpdateProfileImageFromFile()
    {
        $id = 915987427;

        $params = [
            'file' => getcwd() . '/tests/assets/UK.png'
        ];

        $this->assertEndpointCalled(function () use ($id, $params) {
            $this->client->brands($id)->updateImage($params);
        }, "brands/{$id}.json", 'PUT', ['multipart' => true]);
    }
}
