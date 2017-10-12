<?php

namespace Zendesk\API\Resources\Voice;

use Zendesk\API\Traits\Resource\Create;

/**
 * Class Tickets
 * https://developer.zendesk.com/rest_api/docs/voice-api/voice_integration
 */
class Tickets extends ResourceAbstract
{
    /**
     * @inheritdoc
     */
    protected function setUpRoutes()
    {
        $this->setRoute('create', "{$this->getResourceName()}.json");
    }

    /**
     * Search for available voice tickets.
     *
     * @param array $queryParams
     *
     * @return array
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function create(array $params, $routeKey = __FUNCTION__)
    {
        try {
            $route = $this->getRoute($routeKey, $params);
        } catch (RouteException $e) {
            if (!isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName . '.json';
            $this->setRoute(__FUNCTION__, $route);
        }

        return $this->client->post(
            $route,
            $params
        );
    }
}
