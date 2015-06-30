<?php

namespace Zendesk\API\BulkTraits;

use Zendesk\API\Exceptions\RouteException;

/**
 * Allows resources to call a bulk create endpoint.
 *
 */
trait BulkCreateTrait
{
    /**
     * Create multiple new respirces
     *
     * @param array $params
     *
     * @throws ResponseException
     * @throws \Exception
     *
     * @return mixed
     */
    public function createMany(array $params)
    {
        try {
            $route = $this->getRoute(__FUNCTION__);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName . '/create_many.json';
            $this->setRoute('createMany', $route);
        }

        return $this->client->post($route, [self::OBJ_NAME_PLURAL => $params]);
    }
}
