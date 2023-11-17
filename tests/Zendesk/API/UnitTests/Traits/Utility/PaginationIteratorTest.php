<?php

namespace Zendesk\API\UnitTests\Core;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Zendesk\API\Exceptions\ApiResponseException;
use Zendesk\API\Traits\Utility\Pagination\CbpStrategy;
use Zendesk\API\Traits\Utility\Pagination\PaginationError;
use Zendesk\API\Traits\Utility\Pagination\SinglePageStrategy;
use Zendesk\API\UnitTests\BasicTest;
use Zendesk\API\Traits\Utility\Pagination\PaginationIterator;

class MockResource {
    public $params;
    public $foundDifferent = false;
    public $isObp = false;
    public $errorMessage;
    public $response;
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
        if ($this->errorMessage) {
            $request = new Request('GET', 'http://example.zendesk.com');
            $this->response = new Response(400, [], '{ "a": "json"}');
            $requestException = new RequestException($this->errorMessage, $request, $this->response);
            throw new ApiResponseException($requestException);
        } else if ($this->isObp) {
            $this->response = (object) [
                $this->resourceName => $this->resources[0],
                // No CBP meta and links
            ];
        } else {
            // Simulate two pages of resources
            $resources = $this->callCount === 0
                ? $this->resources[0]
                : $this->resources[1];

            // Simulate a cursor for the next page on the first call
            $afterCursor = $this->callCount === 0 ? 'cursor_for_next_page' : null;

            $this->callCount++;
            $this->params = $params;
            $this->response = (object) [
                $this->resourceName => $resources,
                'meta' => (object) [
                    'has_more' => $afterCursor !== null,
                    'after_cursor' => $afterCursor,
                ],
            ];
        }

        return $this->response;
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
        $iterator = new PaginationIterator($mockTickets, $strategy, 'findAll');

        $tickets = $this->iterator_to_array($iterator);

        $this->assertEquals([['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4]], $tickets);
        $this->assertEquals($mockTickets->response, $iterator->latestResponse());
    }

    public function testFetchesTicketsIteratorToArray()
    {
        $this->markTestSkipped("Doesn't work unless you store all pages in the iterator");
        $mockTickets = new MockResource('tickets', [
            [['id' => 1], ['id' => 2]],
            [['id' => 3], ['id' => 4]]
        ]);
        $strategy = new CbpStrategy('tickets', ['page[size]' => 2]);
        $iterator = new PaginationIterator($mockTickets, $strategy, 'findAll');

        $tickets = iterator_to_array($iterator);

        $this->assertEquals([['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4]], $tickets);
        $this->assertEquals($mockTickets->response, $iterator->latestResponse());
    }

    public function testFetchesUsers()
    {
        $mockUsers = new MockResource('users', [
            [['id' => 1, 'name' => 'User 1'], ['id' => 2, 'name' => 'User 2']],
            [['id' => 3, 'name' => 'User 3'], ['id' => 4, 'name' => 'User 4']]
        ]);
        $strategy = new CbpStrategy('users', ['page[size]' => 2]);
        $iterator = new PaginationIterator($mockUsers, $strategy, 'findAll');

        $users = $this->iterator_to_array($iterator);

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
        $iterator = new PaginationIterator($mockTickets, $strategy, 'findAll');

        $tickets = $this->iterator_to_array($iterator);

        $this->assertEquals([['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4]], $tickets);
        $this->assertEquals([
            'any' => 'param',
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
        $iterator = new PaginationIterator($mockResults, $strategy, 'findAll');

        $resources = $this->iterator_to_array($iterator);

        $this->assertEquals([
            ['id' => 1, 'name' => 'Resource 1'],
            ['id' => 2, 'name' => 'Resource 2'],
        ], $resources);
        $this->assertEquals($mockResults->params, $userParams);
    }
    public function testCustomMethod()
    {
        $resultsKey = 'results';
        $userParams = ['param' => 1];
        $mockResults = new MockResource($resultsKey, [
            [['id' => 1, 'name' => 'Resource 1'], ['id' => 2, 'name' => 'Resource 2']]
        ]);
        $strategy = new SinglePageStrategy($resultsKey, $userParams);
        $iterator = new PaginationIterator($mockResults, $strategy, 'findDifferent');

        $resources = $this->iterator_to_array($iterator);

        $this->assertEquals([
            ['id' => 1, 'name' => 'Resource 1'],
            ['id' => 2, 'name' => 'Resource 2'],
        ], $resources);
        $this->assertEquals(true, $mockResults->foundDifferent);
        $this->assertEquals($userParams, $mockResults->params);
    }

    public function testHandlesError()
    {
        $expectedErrorMessage = "BOOM!";
        $resultsKey = 'results';
        $userParams = [];
        $mockResults = new MockResource($resultsKey, []);
        $mockResults->errorMessage = $expectedErrorMessage;
        $strategy = new CbpStrategy($resultsKey, $userParams);
        $iterator = new PaginationIterator($mockResults, $strategy, 'findAll');

        try {
            iterator_to_array($iterator);
        } catch (ApiResponseException $e) {
            $error = $e;
        }

        $this->assertEquals($expectedErrorMessage, $error->getMessage());
        $this->assertEquals([], $error->getErrorDetails());
    }

    public function testErrorsForWrongPagination()
    {
        $mockTickets = new MockResource('tickets', [
            [['id' => 1], ['id' => 2]],
            [['id' => 3], ['id' => 4]]
        ]);
        $mockTickets->isObp = true;
        $strategy = new CbpStrategy('tickets', ['page[size]' => 2]);
        $iterator = new PaginationIterator($mockTickets, $strategy, 'findAll');

        try {
            iterator_to_array($iterator);
        } catch (PaginationError $e) {
            $error = $e;
        }

        $this->assertEquals(
            "Response not conforming to the CBP format, if you think your request is correct, please open an issue at https://github.com/zendesk/zendesk_api_client_php/issues",
            $error->getMessage()
        );
    }
}
