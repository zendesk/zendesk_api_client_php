<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * The OrganizationTickets class exposes tickets methods for organizations
 */
class OrganizationTickets extends ResourceAbstract
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
                'findAll'     => 'organizations/{organization_id}/tickets.json',
            ]
        );
    }

    /**
     * Returns all tickets for a particular organization
     *
     * @param array $queryParams
     *
     * @throws MissingParametersException
     * @throws \Exception
     *
     * @return \stdClass | null
     */
    public function findAll(array $queryParams = [])
    {
        $queryParams = $this->addChainedParametersToParams($queryParams, ['organization_id' => Organizations::class]);

        if (! $this->hasKeys($queryParams, ['organization_id'])) {
            throw new MissingParametersException(__METHOD__, ['organization_id']);
        }

        return $this->traitFindAll($queryParams);
    }

    /*
     * Syntactic sugar methods:
     * Handy aliases:
     */

    /**
     * @param string $id
     * @param array $queryQueryParams
     *
     * @return mixed|void
     * @throws CustomException
     */
    public function find($id = null, array $queryQueryParams = [])
    {
        throw new CustomException('Method ' . __METHOD__
            . ' does not exist. Try $client->ticket()->find(ticket_id) instead.');
    }
}
