<?php

namespace Zendesk\API\Resources\Voice;

use Zendesk\API\Traits\Resource\Create;

/**
 * Class Tickets
 * https://developer.zendesk.com/rest_api/docs/voice-api/voice_integration
 */
class Tickets extends ResourceAbstract
{
    use Create;

    /**
     * @inheritdoc
     */
    protected function setUpRoutes()
    {
        $this->setRoute('create', "{$this->getResourceName()}.json");
    }

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
