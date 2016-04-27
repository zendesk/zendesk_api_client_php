<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Exceptions\RouteException;

trait CreateOrUpdate
{
    /**
     * Create a new resource
     *
     * @param array $params
     *
     * @throws \Exception
     * @return mixed
     */
    public function createOrUpdate(array $params, $routeKey = __FUNCTION__)
    {
        try {
            $route = $this->getRoute($routeKey, $params);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName . '/create_or_update.json';
            $this->setRoute(__FUNCTION__, $route);
        }

        return $this->client->post(
            $route,
            [$this->objectName => $params]
        );
    }
}
