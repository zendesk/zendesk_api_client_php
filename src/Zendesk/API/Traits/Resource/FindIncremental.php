<?php

namespace Zendesk\API\Traits\Resource;

/**
 * 
 * @package    Zendesk\API\Traits\Resource
 * @author     Muhammad Yousaf Saqib <yousafsaqib979@gmail.com>
 */

use Zendesk\API\Exceptions\RouteException;

trait FindIncremental
{
    /**
     * List all of this resource incrementally
     *
     * @param array  $params
     *
     * @param string $routeKey
     *
     * @return \stdClass | null
     * @throws \Zendesk\API\Exceptions\AuthException
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     */
    public function findIncremental(array $params = [], $routeKey = __FUNCTION__)
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
