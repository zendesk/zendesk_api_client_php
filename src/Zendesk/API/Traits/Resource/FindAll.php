<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Exceptions\RouteException;
use Zendesk\API\Traits\Utility\Pagination\CbpStrategy;
use Zendesk\API\Traits\Utility\Pagination\PaginationIterator;

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
            if (!isset($this->resourceName)) {
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

    // TODO: own trait
    // TODO: page size 100
    /**
     * Usage:
     * foreach ($ticketsIterator as $ticket) {
     *     process($ticket)
     * }
     *
     * @return PaginationIterator to fetch all pages.
     */
    public function iterator()
    {
        $strategyClass = $this->paginationStrategyClass();
        $strategy = new $strategyClass($this, $this->resourcesRoot(), 2);
        return new PaginationIterator($strategy);
    }


    private function paginationStrategyClass() {
        return CbpStrategy::class;
    }

    protected function resourcesRoot() {
        return $this->objectNamePlural;
    }
}
