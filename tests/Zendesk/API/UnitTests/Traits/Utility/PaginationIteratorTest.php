<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Zendesk\API\Exceptions\ApiResponseException;
use Zendesk\API\Traits\Utility\Pagination\CbpStrategy;
use Zendesk\API\Traits\Utility\Pagination\SinglePageStrategy;
use Zendesk\API\UnitTests\BasicTest;
use Zendesk\API\Traits\Utility\Pagination\PaginationIterator;

class MockResource {
    public $params;
    public $foundDifferent = false;
    private $resources;
    private $resourceName;
    private $callCount = 0;
    private $errorMessage;

    public function __construct($resourceName, $resources, $errorMessage = null)
    {
        $this->resourceName = $resourceName;
        $this->resources = $resources;
        $this->callCount = 0;
        $this->errorMessage = $errorMessage;
    }

    public function findAll($params)
    {
        if ($this->errorMessage) {
            $request = new Request('GET', 'http://example.zendesk.com');
            $response = new Response(400, [], '{ "a": "json"}');
            $requestException = new RequestException($this->errorMessage, $request, $response);
            throw new ApiResponseException($requestException);
        }
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

    public function findDifferent($params)
    {
        $this->foundDifferent = true;
        return $this->findAll($params);
    }
}

class PaginationIteratorTest extends BasicTest
{
    public function testFetchesTickets()
    {
        $mockTickets = new MockResource('tickets', [
            [['id' => 1], ['id' => 2]],
            [['id' => 3], ['id' => 4]]
        ]);
        $strategy = new CbpStrategy('tickets', ['page[size]' => 2]);
        $iterator = new PaginationIterator($mockTickets, $strategy);

        // WORKS
        $tickets = [];
        foreach ($iterator as $ticket) {
            print("!!!!!!!!!!!! LOOP \n");
            print_r($tickets); print(" \n");
            $tickets[] = $ticket;
        }

        // DOESN'T WORK
        // $tickets = iterator_to_array($iterator);

        $this->assertEquals([['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4]], $tickets);
    }

    // public function testFetchesUsers()
    // {
    //     $mockUsers = new MockResource('users', [
    //         [['id' => 1, 'name' => 'User 1'], ['id' => 2, 'name' => 'User 2']],
    //         [['id' => 3, 'name' => 'User 3'], ['id' => 4, 'name' => 'User 4']]
    //     ]);
    //     $strategy = new CbpStrategy('users', ['page[size]' => 2]);
    //     $iterator = new PaginationIterator($mockUsers, $strategy);

    //     $users = iterator_to_array($iterator);

    //     $this->assertEquals([
    //         ['id' => 1, 'name' => 'User 1'],
    //         ['id' => 2, 'name' => 'User 2'],
    //         ['id' => 3, 'name' => 'User 3'],
    //         ['id' => 4, 'name' => 'User 4']
    //     ], $users);
    // }

    // public function testFetchesCbpWithParams()
    // {
    //     $mockTickets = new MockResource('tickets', [
    //         [['id' => 1], ['id' => 2]],
    //         [['id' => 3], ['id' => 4]]
    //     ]);
    //     $strategy = new CbpStrategy('tickets', ['page[size]' => 2, 'any' => 'param']);
    //     $iterator = new PaginationIterator($mockTickets, $strategy);

    //     $tickets = iterator_to_array($iterator);

    //     $this->assertEquals([['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4]], $tickets);
    //     $this->assertEquals([
    //         'any' => 'param',
    //         'page[size]' => 2, 'page[after]' => 'cursor_for_next_page'
    //     ], $mockTickets->params);
    // }

    // public function testCorrectsParamsToCbp()
    // {
    //     $mockTickets = new MockResource('tickets', [
    //         [['id' => 1], ['id' => 2]],
    //         [['id' => 3], ['id' => 4]]
    //     ]);
    //     $strategy = new CbpStrategy('tickets', ['per_page' => 2, 'sort_by' => 'id', 'sort_order' => 'desc']);
    //     $iterator = new PaginationIterator($mockTickets, $strategy);

    //     iterator_to_array($iterator);

    //     $this->assertEquals([
    //         'sort' => '-id',
    //         'page[size]' => 2, 'page[after]' => 'cursor_for_next_page'
    //     ], $mockTickets->params);
    // }

    // public function testFetchesSinglePageWithParams()
    // {
    //     $resultsKey = 'results';
    //     $userParams = ['param' => 1];
    //     $mockResults = new MockResource($resultsKey, [
    //         [['id' => 1, 'name' => 'Resource 1'], ['id' => 2, 'name' => 'Resource 2']]
    //     ]);
    //     $strategy = new SinglePageStrategy($resultsKey, $userParams);
    //     $iterator = new PaginationIterator($mockResults, $strategy);

    //     $resources = iterator_to_array($iterator);

    //     $this->assertEquals([
    //         ['id' => 1, 'name' => 'Resource 1'],
    //         ['id' => 2, 'name' => 'Resource 2'],
    //     ], $resources);
    //     $this->assertEquals($mockResults->params, $userParams);
    // }
    // public function testCustomMethod()
    // {
    //     $resultsKey = 'results';
    //     $userParams = ['param' => 1];
    //     $mockResults = new MockResource($resultsKey, [
    //         [['id' => 1, 'name' => 'Resource 1'], ['id' => 2, 'name' => 'Resource 2']]
    //     ]);
    //     $strategy = new SinglePageStrategy($resultsKey, $userParams);
    //     $iterator = new PaginationIterator($mockResults, $strategy, 'findDifferent');

    //     $resources = iterator_to_array($iterator);

    //     $this->assertEquals([
    //         ['id' => 1, 'name' => 'Resource 1'],
    //         ['id' => 2, 'name' => 'Resource 2'],
    //     ], $resources);
    //     $this->assertEquals(true, $mockResults->foundDifferent);
    //     $this->assertEquals($userParams, $mockResults->params);
    // }

    // public function testHandlesError()
    // {
    //     $expectedErrorMessage = "BOOM!";
    //     $resultsKey = 'results';
    //     $userParams = [];
    //     $mockResults = new MockResource($resultsKey, [], $expectedErrorMessage);
    //     $strategy = new CbpStrategy($resultsKey, $userParams);
    //     $iterator = new PaginationIterator($mockResults, $strategy);

    //     try {
    //         iterator_to_array($iterator);
    //         $actualErrorMessage = null;
    //     } catch (ApiResponseException $e) {
    //         $actualErrorMessage = $e->getMessage();
    //     }

    //     $this->assertEquals($expectedErrorMessage, $actualErrorMessage);
    // }
}
