<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * The UserTickets class exposes ticket methods for users
 */
class UserTickets extends ResourceAbstract
{
    use FindAll {
        findAll as traitFindAll;
    }

    /**
     * {@inheritdoc}
     */
    protected $objectName = 'ticket';
    /**
     * {@inheritdoc}
     */
    protected $objectNamePlural = 'tickets';

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        $this->setRoutes(
            [
                'requested'     => 'users/{user_id}/tickets/requested.json',
                'assigned'     => 'users/{user_id}/tickets/assigned.json',
                'ccd'     => 'users/{user_id}/tickets/ccd.json',
            ]
        );
    }

    /**
     * Returns all requested tickets for a particular user
     *
     * @param array $queryParams
     *
     * @throws MissingParametersException
     * @throws \Exception
     *
     * @return mixed
     */
    public function requested(array $queryParams = [])
    {
        $queryParams = $this->addChainedParametersToParams($queryParams, ['user_id' => Users::class]);

        if (! $this->hasKeys($queryParams, ['user_id'])) {
            throw new MissingParametersException(__METHOD__, ['user_id']);
        }

        return $this->traitFindAll($queryParams);
    }

    /**
     * Returns all ccd'ed tickets for a particular user
     *
     * @param array $queryParams
     *
     * @throws MissingParametersException
     * @throws \Exception
     *
     * @return mixed
     */
    public function ccd(array $queryParams = [])
    {
        $queryParams = $this->addChainedParametersToParams($queryParams, ['user_id' => Users::class]);

        if (! $this->hasKeys($queryParams, ['user_id'])) {
            throw new MissingParametersException(__METHOD__, ['user_id']);
        }

        return $this->traitFindAll($queryParams);
    }

    /**
     * Returns all assigned tickets for a particular user
     *
     * @param array $queryParams
     *
     * @throws MissingParametersException
     * @throws \Exception
     *
     * @return mixed
     */
    public function assigned(array $queryParams = [])
    {
        $queryParams = $this->addChainedParametersToParams($queryParams, ['user_id' => Users::class]);

        if (! $this->hasKeys($queryParams, ['user_id'])) {
            throw new MissingParametersException(__METHOD__, ['user_id']);
        }

        return $this->traitFindAll($queryParams);
    }

    /*
     * Syntactic sugar methods:
     * Handy aliases:
     */

    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws CustomException
     */
    public function find($id = null, array $queryQueryParams = [])
    {
        throw new CustomException('Method ' . __METHOD__
            . ' does not exist. Try $client->tickets()->find(ticket_id) instead.');
    }
}
