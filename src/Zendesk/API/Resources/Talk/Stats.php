<?php

namespace Zendesk\API\Resources\Talk;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Exceptions\ResponseException;
use Zendesk\API\Http;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * The Stats class exposes key methods for reading Talk Stats.
 */
class Stats extends ResourceAbstract
{
    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            "currentQueue"      => "{$this->prefix}stats/current_queue_activity.json",
            "accountOverview"   => "{$this->prefix}stats/account_overview.json",
            "agentsOverview"    => "{$this->prefix}stats/agents_overview.json",
            "agentsActivity"    => "{$this->prefix}stats/agents_activity.json",
        ]);
    }

    /**
     * Shows current queue.
     *
     * @throws \Exception
     * @return \stdClass | null
     * @throws \Zendesk\API\Exceptions\AuthException
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     */
    public function currentQueue()
    {
        $route = $this->getRoute(__FUNCTION__);

        return $this->client->get($route);
    }
    
    /**
     * Account overview.
     *
     * @throws \Exception
     * @return \stdClass | null
     * @throws \Zendesk\API\Exceptions\AuthException
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     */
    public function accountOverview()
    {
        $route = $this->getRoute(__FUNCTION__);

        return $this->client->get($route);
    }
    
    /**
     * Agents overview.
     *
     * @throws \Exception
     * @return \stdClass | null
     * @throws \Zendesk\API\Exceptions\AuthException
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     */
    public function agentsOverview()
    {
        $route = $this->getRoute(__FUNCTION__);

        return $this->client->get($route);
    }
    
    /**
     * Agents activity.
     *
     * @throws \Exception
     * @return \stdClass | null
     * @throws \Zendesk\API\Exceptions\AuthException
     * @throws \Zendesk\API\Exceptions\ApiResponseException
     */
    public function agentsActivity()
    {
        $route = $this->getRoute(__FUNCTION__);

        return $this->client->get($route);
    }
}
