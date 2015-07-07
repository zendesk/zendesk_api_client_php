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
    public function create(array $params)
    {
        try {
            $route = $this->getRoute(__FUNCTION__);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName . '.json';
            $this->setRoute(__FUNCTION__, $route);
        }

        $class = get_class($this);

        return $this->client->post(
            $route,
            [$class::OBJ_NAME => $params]
        );
    }
}
