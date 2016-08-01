<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Exceptions\RouteException;

/**
 * Allows resources to call a bulk createOrUpdate endpoint.
 */
trait CreateOrUpdateMany
{

    /**
     * Update group of resources
     *
     * @param array  $params
     *
     * @return \stdClass | null
     */
    public function createOrUpdateMany(array $params)
    {
        try {
            $route = $this->getRoute(__FUNCTION__);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName . '/create_or_update_many.json';
            $this->setRoute('createOrUpdateMany', $route);
        }


        $response = $this->client->post(
            $route,
            [$this->objectNamePlural => $params]
        );

        return $response;
    }
}
