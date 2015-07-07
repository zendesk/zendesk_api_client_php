<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Exceptions\RouteException;

trait FindAll
{
    /**
     * List all of this resource
     *
     * @param array $params
     *
     * @throws \Exception
     * @return mixed
     */
    public function findAll(array $params = [])
    {
        try {
            $route = $this->getRoute(__FUNCTION__, $params);
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
