<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Exceptions\RouteException;

trait FindAll
{
    /**
     * List all of this resource
     *
     * @param array  $params
     *
     * @param string $routeKey
     *
     * @return \stdClass | null
     * @throws \Zendesk\API\Exceptions\AuthException
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     */
    public function findAll(array $params = [], $routeKey = __FUNCTION__)
    {
        try {
            $route = $this->getRoute($routeKey, $params);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName . '.json';
            $this->setRoute(__FUNCTION__, $route);
        }

        return $this->client->get(
            $route,
            $params
        );
    }
}

trait FindPaginating {
    public function findPaginating($routeKey = __FUNCTION__)
    {
        $params = ['page[size]' => 100];
        while (true) {
            // findAll
            try {
                $route = $this->getRoute($routeKey, $params);
            } catch (RouteException $e) {
                if (! isset($this->resourceName)) {
                    $this->resourceName = $this->getResourceNameFromClass();
                }

                $route = $this->resourceName . '.json';
                $this->setRoute(__FUNCTION__, $route);
            }

            $response = $this->client->get(
                $route,
                $params
            );
            // findAll end

            if (empty($response->tickets)) {
                break;
            }

            echo "findPaginating: " . count($response->tickets) . "\n";

            // Check if there are more pages
            if (!$response->meta->has_more) {
                break;
            }

            // Get the next page
            $params['page[after]'] = $response->meta->after_cursor;
        }
    }
}
