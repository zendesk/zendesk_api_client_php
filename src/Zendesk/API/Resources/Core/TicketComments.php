<?php

namespace Zendesk\API\Resources\Core;

use Zendesk\API\Exceptions\CustomException;
use Zendesk\API\Exceptions\MissingParametersException;
use Zendesk\API\Resources\ResourceAbstract;
use Zendesk\API\Traits\Resource\FindAll;

/**
 * The TicketComments class exposes comment methods for tickets
 */
class TicketComments extends ResourceAbstract
{
    use FindAll {
        findAll as traitFindAll;
    }

    /**
     * {@inheritdoc}
     */
    protected $objectName = 'comment';
    /**
     * {@inheritdoc}
     */
    protected $objectNamePlural = 'comments';

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes()
    {
        $this->setRoutes(
            [
                'findAll'     => 'tickets/{ticket_id}/comments.json',
                'makePrivate' => 'tickets/{ticket_id}/comments/{id}/make_private.json'
            ]
        );
    }

    /**
     * Returns all comments for a particular ticket
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
        $queryParams = $this->addChainedParametersToParams($queryParams, ['ticket_id' => Tickets::class]);

        if (! $this->hasKeys($queryParams, ['ticket_id'])) {
            throw new MissingParametersException(__METHOD__, ['ticket_id']);
        }

        return $this->traitFindAll($queryParams);
    }

    /**
     * Make the specified comment private
     *
     * @param array $params
     *
     * @throws MissingParametersException
     * @throws \Exception
     *
     * @return \stdClass | null
     */
    public function makePrivate(array $params = [])
    {
        $params = $this->addChainedParametersToParams(
            $params,
            ['id' => get_class($this), 'ticket_id' => Tickets::class]
        );

        if (! $this->hasKeys($params, ['id', 'ticket_id'])) {
            throw new MissingParametersException(__METHOD__, ['id', 'ticket_id']);
        }

        return $this->client->put($this->getRoute(__FUNCTION__, $params), $params);
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
                                  . ' does not exist. Try $client->tickets(ticket_id)->comments()->findAll() instead.');
    }
}
