<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Exceptions\ResponseException;
use Zendesk\API\Http;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Utility\InstantiatorTrait;

/**
 * The UserTickets class exposes methods to retrieve tickets created, cc'ed to, or
 * assigned to a user
 */
class UserTickets extends ResourceAbstract
{
    use InstantiatorTrait;

    /**
     * Wrapper for common GET requests
     *
     * @param $route
     * @param array $params
     *
     * @return \stdClass | null
     * @throws ResponseException
     * @throws \Exception
     */
    private function sendGetRequest($route, array $params = [])
    {
        $response = Http::send(
            $this->client,
            $this->getRoute($route, $params),
            ['queryParams' => $params]
        );

        return $response;
    }

    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes(
            [
            'requested' => 'users/{id}/tickets/requested.json',
            'ccd'       => 'users/{id}/tickets/ccd.json',
            'assigned'  => 'users/{id}/tickets/assigned.json',
            ]
        );
    }

    /**
     * List tickets that a user requested
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return \stdClass | null
     */
    public function requested(array $params = [])
    {
        $params = $this->addChainedParametersToParams(
            $params,
            ['id' => Users::class]
        );

        if (! $this->hasKeys($params, ['id'])) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        return $this->sendGetRequest(__FUNCTION__, $params);
    }

    /**
     * List tickets a user was CC'ed on
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return \stdClass | null
     */
    public function ccd(array $params = [])
    {
        $params = $this->addChainedParametersToParams(
            $params,
            ['id' => Users::class]
        );

        if (! $this->hasKeys($params, ['id'])) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        return $this->sendGetRequest(__FUNCTION__, $params);
    }

    /**
     * List tickets a user was assigned
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws ResponseException
     * @throws \Exception
     * @return \stdClass | null
     */
    public function assigned(array $params = [])
    {
        $params = $this->addChainedParametersToParams(
            $params,
            ['id' => Users::class]
        );

        if (! $this->hasKeys($params, ['id'])) {
            throw new MissingParametersException(__METHOD__, ['id']);
        }

        return $this->sendGetRequest(__FUNCTION__, $params);
    }
}
