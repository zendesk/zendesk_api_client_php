<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;

/**
 * Class BookmarksTest
 * https://developer.zendesk.com/rest_api/docs/core/bookmarks
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
