<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Exceptions\RouteException;

trait Search
{
    /**
     * Used to access the search endpoint of Resources
     *
     * @param array $params query parameters
     * @return null|\stdClass
     */
    public function search(array $params)
    {
        try {
            $route = $this->getRoute(__FUNCTION__, $params);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName . '/search.json';
            $this->setRoute(__FUNCTION__, $route);
        }

        return $this->client->get($route, $params);
    }
}
