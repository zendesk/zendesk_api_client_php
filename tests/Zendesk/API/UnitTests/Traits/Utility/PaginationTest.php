<?php

namespace Zendesk\API\UnitTests\Core;

use Zendesk\API\Traits\Utility\Pagination\CbpStrategy;
use Zendesk\API\Traits\Utility\Pagination\SinglePageStrategy;
use Zendesk\API\UnitTests\BasicTest;
use Zendesk\API\Traits\Utility\Pagination\PaginationIterator;

class MockResource {
    public $params;
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

        $this->params = $params;

        return (object) [
            $this->resourceName => $resources,
            'meta' => (object) [
                'has_more' => $afterCursor !== null,
                'after_cursor' => $afterCursor,
            ],
        ];
    }
}

class PaginationTest extends BasicTest
{
    public function testFetchesTickets()
    {
        $mockTickets = new MockResource('tickets', [
            [['id' => 1], ['id' => 2]],
            [['id' => 3], ['id' => 4]]
        ]);
        $strategy = new CbpStrategy('tickets', ['page[size]' => 2]);
        $iterator = new PaginationIterator($mockTickets, $strategy);

        $tickets = iterator_to_array($iterator);

        $this->assertEquals([['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4]], $tickets);
    }

    public function testFetchesUsers()
    {
        $mockUsers = new MockResource('users', [
            [['id' => 1, 'name' => 'User 1'], ['id' => 2, 'name' => 'User 2']],
            [['id' => 3, 'name' => 'User 3'], ['id' => 4, 'name' => 'User 4']]
        ]);
        $strategy = new CbpStrategy('users', ['page[size]' => 2]);
        $iterator = new PaginationIterator($mockUsers, $strategy);

        $users = iterator_to_array($iterator);

        $this->assertEquals([
            ['id' => 1, 'name' => 'User 1'],
            ['id' => 2, 'name' => 'User 2'],
            ['id' => 3, 'name' => 'User 3'],
            ['id' => 4, 'name' => 'User 4']
        ], $users);
    }

    public function testFetchesCbpWithParams()
    {
        $mockTickets = new MockResource('tickets', [
            [['id' => 1], ['id' => 2]],
            [['id' => 3], ['id' => 4]]
        ]);
        $strategy = new CbpStrategy('tickets', ['page[size]' => 2, 'any' => 'param']);
        $iterator = new PaginationIterator($mockTickets, $strategy);

        $tickets = iterator_to_array($iterator);

        $this->assertEquals([['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4]], $tickets);
        $this->assertEquals([
            'any' => 'param',
            'page[size]' => 2, 'page[after]' => 'cursor_for_next_page'
        ], $mockTickets->params);
    }

    public function testCorrectsParamsToCbp()
    {
        $mockTickets = new MockResource('tickets', [
            [['id' => 1], ['id' => 2]],
            [['id' => 3], ['id' => 4]]
        ]);
        $strategy = new CbpStrategy('tickets', ['per_page' => 2, 'sort_by' => 'id', 'sort_order' => 'desc']);
        $iterator = new PaginationIterator($mockTickets, $strategy);

        iterator_to_array($iterator);

        $this->assertEquals([
            'sort' => '-id',
            'page[size]' => 2, 'page[after]' => 'cursor_for_next_page'
        ], $mockTickets->params);
    }

    public function testFetchesSinglePageWithParams()
    {
        $resultsKey = 'results';
        $userParams = ['param' => 1];
        $mockResults = new MockResource($resultsKey, [
            [['id' => 1, 'name' => 'Resource 1'], ['id' => 2, 'name' => 'Resource 2']]
        ]);
        $strategy = new SinglePageStrategy($resultsKey, $userParams);
        $iterator = new PaginationIterator($mockResults, $strategy);

        $resources = iterator_to_array($iterator);

        $this->assertEquals([
            ['id' => 1, 'name' => 'Resource 1'],
            ['id' => 2, 'name' => 'Resource 2'],
        ], $resources);
        $this->assertEquals($mockResults->params, $userParams);
    }
}
