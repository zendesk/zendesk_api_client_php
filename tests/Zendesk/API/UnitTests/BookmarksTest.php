<?php

namespace Zendesk\API\UnitTests;

/**
 * Class BookmarksTest
 * @package Zendesk\API\UnitTests
 */
class BookmarksTest extends BasicTest
{
    /**
     * Test the traits included are available
     */
    public function testRoutes()
    {
        $this->assertTrue(method_exists($this->client->bookmarks(), 'findAll'));
        $this->assertTrue(method_exists($this->client->bookmarks(), 'create'));
        $this->assertTrue(method_exists($this->client->bookmarks(), 'delete'));
    }
}
