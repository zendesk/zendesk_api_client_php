<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Exceptions\RouteException;

/**
 * Allows resources to call a createOrUpdate endpoint.
 */
trait CreateOrUpdate
{

    /**
     * Update resource
     *
     * @param array  $params
     *
     * @return \stdClass | null
     */
    public function createOrUpdate(array $params)
    {
        try {
            $route = $this->getRoute(__FUNCTION__, $params);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName . '/create_or_update.json';
            $this->setRoute(__FUNCTION__, $route);
        }


        $response = $this->client->post(
            $route,
            [$this->objectName => $params]
        );

        return $response;
    }
}
