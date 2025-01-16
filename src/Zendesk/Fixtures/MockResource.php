<?php

namespace Zendesk\Fixtures;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Zendesk\API\Exceptions\ApiResponseException;

class MockResource
{
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
        } elseif ($this->isObp) {
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
