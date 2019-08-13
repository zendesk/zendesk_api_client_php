<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Exceptions\RouteException;

trait Translation
{
    /**
     * Updates translation of a resource
     *
     * @param null $id
     * @param array $updateResourceFields
     * @param string $routeKey
     * @return mixed
     */
    public function translations($id = null, array $updateResourceFields = [], $routeKey = __FUNCTION__)
    {
        $class = get_class($this);
        if (empty($id)) {
            $id = $this->getChainedParameter($class);
        }

        try {
            $route = $this->getRoute($routeKey, ['id' => $id]);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $this->setRoute(__FUNCTION__, $this->resourceName . '/{id}/translations/'.$this->getLocale().'.json');
            $route = $this->resourceName . '/' . $id . '/translations/'.$this->getLocale().'.json';
        }

        return $this->client->put(
            $route,
            ['translation'=> $updateResourceFields]
        );
    }
}
