<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Exceptions\RouteException;

/**
 * Allows resources to call a bulk show endpoint.
 *
 */
trait FindMany
{
    /**
     * Show multiple resources
     *
     * @param array  $ids         Array of IDs to fetch
     * @param array  $extraParams Extra query parameters such as sideloads and iterators
     * @param string $key         Could be `id` or `external_ids`
     *
     * @return \stdClass | null
     *
     */
    public function findMany(array $ids = [], $extraParams = [], $key = 'ids')
    {
        try {
            $route = $this->getRoute(__FUNCTION__);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName . '/show_many.json';
            $this->setRoute('findMany', $route);
        }

        $queryParams = [];

        if (count($ids) > 0) {
            $queryParams[$key] = implode(',', $ids);
        }

        return $this->client->get($route, array_merge($queryParams, $extraParams));
    }
}
