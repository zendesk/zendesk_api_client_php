<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Exceptions\RouteException;
use Zendesk\API\Http;

/**
 * Allows resources to call a bulk show endpoint.
 */
trait UpdateMany
{

    /**
     * Update group of resources
     *
     * @param array  $params
     * @param string $key Could be `id` or `external_id`
     *
     * @return \stdClass | null
     */
    public function updateMany(array $params, $key = 'ids')
    {
        try {
            $route = $this->getRoute(__FUNCTION__);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName . '/update_many.json';
            $this->setRoute('updateMany', $route);
        }

        $resourceUpdateName = $this->objectNamePlural;
        $queryParams        = [];
        if (isset($params[$key]) && is_array($params[$key])) {
            $queryParams[$key] = implode(',', $params[$key]);
            unset($params[$key]);

            $resourceUpdateName = $this->objectName;
        }

        $response = Http::send(
            $this->client,
            $route,
            [
                'queryParams' => $queryParams,
                'postFields'  => [$resourceUpdateName => $params],
                'method'      => 'PUT'
            ]
        );

        $this->client->setSideload(null);

        return $response;
    }
}
