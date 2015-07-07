<?php

namespace Zendesk\API\Traits\Resource;

use Zendesk\API\Exceptions\RouteException;

trait Update
{

    /**
     * Update a resource
     *
     * @param array $updateResourceFields
     *
     * @throws MissingParametersException
     * @throws \Exception
     * @return mixed
     */
    public function update($id = null, array $updateResourceFields = [])
    {
        $class = get_class($this);
        if (empty($id)) {
            $id = $this->getChainedParameter($class);
        }

        try {
            $route = $this->getRoute(__FUNCTION__, ['id' => $id]);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $this->setRoute(__FUNCTION__, $this->resourceName . '/{id}.json');
            $route = $this->resourceName . '/' . $id . '.json';
        }


        return $this->client->put(
            $route,
            [$class::OBJ_NAME => $updateResourceFields]
        );
    }
}
