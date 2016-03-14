<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Exceptions\RouteException;

trait Create
{
    /**
     * Create a new resource
     *
     * @param array $params
     *
     * @throws \Exception
     * @return mixed
     */
    public function create(array $params, $routeKey = __FUNCTION__)
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

        return $this->client->post(
            $route,
            [$this->objectName => $params]
        );
    }
}
