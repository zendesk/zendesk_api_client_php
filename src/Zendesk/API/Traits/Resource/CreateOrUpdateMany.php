<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Exceptions\RouteException;
use Zendesk\API\Http;

/**
 * Allows resources to call a bulk createOrUpdate endpoint.
 */
trait CreateOrUpdateMany
{

    /**
     * Update group of resources
     *
     * @param array  $params
     * @param string $key Could be `id`, `external_id` or `email`
     *
     * @return mixed
     */
    public function createOrUpdateMany(array $params, $key = 'ids')
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
                'method'      => 'POST'
            ]
        );

        $this->client->setSideload(null);

        return $response;
    }
}
