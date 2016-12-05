<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\Find;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * The Locales class exposes view management methods
 */
class Locales extends ResourceAbstract
{
    use Find;
    use FindAll;

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        $this->setRoutes([
            'findAllPublic' => "{$this->resourceName}/public.json",
            'findAllAgent'  => "{$this->resourceName}/agent.json",
            'findCurrent'   => "{$this->resourceName}/current.json",
            'findBest'      => "{$this->resourceName}/detect_best_locale.json",
        ]);
    }

    /**
     * This lists the translation locales that are available to all accounts
     *
     * @param array $params
     *
     * @throws \Exception
     *
     * @return \stdClass | null
     */
    public function findAllPublic(array $params = [])
    {
        return $this->findAll($params, __FUNCTION__);
    }

    /**
     * This lists the translation locales that have been localized for agents.
     *
     * @param array $params
     *
     * @throws \Exception
     *
     * @return \stdClass | null
     */
    public function findAllAgent(array $params = [])
    {
        return $this->findAll($params, __FUNCTION__);
    }

    /**
     * This works exactly like show, but instead of taking an id as argument,
     * it renders the locale of the user performing the request
     *
     * @param array $params
     *
     * @throws \Exception
     *
     * @return \stdClass | null
     */
    public function findCurrent(array $params = [])
    {
        return $this->findAll($params, __FUNCTION__);
    }

    /**
     * Detect best language for user
     *
     * @param array $params
     *
     * @throws \Exception
     *
     * @return \stdClass | null
     */
    public function findBest(array $params = [])
    {
        return $this->findAll($params, __FUNCTION__);
    }
}
