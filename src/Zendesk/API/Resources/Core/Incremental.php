<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;

/**
 * Class IncrementalExports
 * https://developer.zendesk.com/rest_api/docs/core/incremental_export
 */
class Incremental extends ResourceAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        $this->setRoutes([
            'tickets'       => "{$this->resourceName}/tickets.json",
            'ticketEvents'  => "{$this->resourceName}/ticket_events.json",
            'organizations' => "{$this->resourceName}/organizations.json",
            'users'         => "{$this->resourceName}/users.json",
        ]);
    }

    /**
     * Incremental Ticket Export
     *
     * @param array $params
     *
     * @return \stdClass | null
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function tickets(array $params)
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $params);
    }

    /**
     * The Ticket Events Incremental Export API returns a an stream changes that have occurred on tickets.
     *
     * @param array $params
     *
     * @return \stdClass | null
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function ticketEvents(array $params)
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $params);
    }

    /**
     * Get information about organizations updated since a given point in time
     *
     * @param array $params
     *
     * @return \stdClass | null
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function organizations(array $params)
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $params);
    }

    /**
     * Get information about users updated since a given point in time
     *
     * @param array $params
     *
     * @return \stdClass | null
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function users(array $params)
    {
        return $this->client->get($this->getRoute(__FUNCTION__), $params);
    }
}
