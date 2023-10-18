<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\UnitTests\BasicTest;
use Zendesk\API\Traits\Utility\CbpIterator;

class MockResource {
    private $resources;
    private $resourceName;
    private $callCount = 0;

    public function __construct($resourceName, $resources)
    {
        $this->resourceName = $resourceName;
        $this->resources = $resources;
        $this->callCount = 0;
    }

    public function findAll($params)
    {
        // Simulate two pages of resources
        $resources = $this->callCount === 0
            ? $this->resources[0]
            : $this->resources[1];

        // Simulate a cursor for the next page on the first call
        $afterCursor = $this->callCount === 0 ? 'cursor_for_next_page' : null;

        $this->callCount++;

        return (object) [
            $this->resourceName => $resources,
            'meta' => (object) [
                'has_more' => $afterCursor !== null,
                'after_cursor' => $afterCursor,
            ],
        ];
    }
}

class CbpIteratorTest extends BasicTest
{
    public function testFetchesTickets()
    {
        $mockTickets = new MockResource('tickets', [
            [['id' => 1], ['id' => 2]],
            [['id' => 3], ['id' => 4]]
        ]);
        $iterator = new CbpIterator($mockTickets, 'tickets', 2);

        $tickets = iterator_to_array($iterator);

        $this->assertEquals([['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4]], $tickets);
    }

    public function testFetchesUsers()
    {
        $mockUsers = new MockResource('users', [
            [['id' => 1, 'name' => 'User 1'], ['id' => 2, 'name' => 'User 2']],
            [['id' => 3, 'name' => 'User 3'], ['id' => 4, 'name' => 'User 4']]
        ]);
        $iterator = new CbpIterator($mockUsers, 'users', 2);

        $users = iterator_to_array($iterator);

        $this->assertEquals([
            ['id' => 1, 'name' => 'User 1'],
            ['id' => 2, 'name' => 'User 2'],
            ['id' => 3, 'name' => 'User 3'],
            ['id' => 4, 'name' => 'User 4']
        ], $users);
    }
}
